<?php

namespace App\Services;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    public function index()
    {
        return $this->requestStack->getSession()->get('cart') ?? [];
    }

    public function add(Product $product, int $quantity)
    {
        $cart = $this->requestStack->getSession()->get('cart');

        if (!$cart) {
            $this->newCart($product, $quantity);
            return;
        }
        if(isset($cart[$product->getId()])) {
            $this->updateQuantity($cart, $quantity);
            return;
        }

        
        $cart[$product->getId()] = [
            "id" => $product->getId(),
            "name" => $product->getTitle(),
            "quantity" => $quantity,
            "price" => $product->getPrie(),
            "photo" => $product->getImagePath()
        ];

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function addQuantity(int $cartId)
    {
        $cart = $this->requestStack->getSession()->get('cart');

        $cart[$cartId]['quantity']++;

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function removeQuantity(int $cartId)
    {
        $cart = $this->requestStack->getSession()->get('cart');

        $cart[$cartId]['quantity']--;

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function removeAll()
    {
        $this->requestStack->getSession()->set('cart', []);
    }

    public function removeItem(int $productId)
    {
        $cart = $this->requestStack->getSession()->get('cart');

        unset($cart[$productId]);

        $this->requestStack->getSession()->set('cart', $cart);
    }

    private function updateQuantity($cart, int $quantity)
    {
        $cart['id']['quantity']+=$quantity;
        $this->requestStack->getSession()->set('cart', $cart);
    }

    private function newCart(Product $product, $quantity)
    {
        $cart[$product->getId()] = [
            "id" => $product->getId(),
            "name" => $product->getTitle(),
            "quantity" => $quantity,
            "price" => $product->getPrie(),
            "photo" => $product->getImagePath()
        ];

        $this->requestStack->getSession()->set('cart', $cart);
    }
}
