<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\CartService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;

final class CartController extends AbstractController
{

    public function __construct(
        private CartService $cartService,
    ) {
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService)
    {
        $cart = $cartService->index() ?? [];
        $quantities = array_column($cart, 'quantity');
        $prices = array_column($cart, 'price');

        $totalQuantity = array_sum($quantities);
        $totalPrice = array_sum(array_map(function ($item ) { 
           return $item['quantity'] * floatval($item['price']);
        }, $cart)); 

        return $this->render('cart/index.html.twig', ['cart' => $cart, 'totalPrice' => $totalPrice, 'totalQuantity' => $totalQuantity]);
    }

    public function add(Request $request, CartService $cartService, Product $product)
    {
        $cartService->add($product, $request->get('quantity'));
        $cart = $cartService->index();

        $quantities = array_column($cart, 'quantity');
        $totalQuantity = array_sum($quantities);
        $totalPrice = array_sum(array_map(function ($item ) { 
            return $item['quantity'] * floatval($item['price']);
         }, $cart)); 
 

        return $this->json([
            'success' => 'Cart was added succesfully',
            'items' => $totalQuantity,
            'totalPrice' => $totalPrice   
        ]);
    }

    public function removeAll(CartService $cartService, Request $request)
    {
        $cartService->removeAll();
        $referer = $request->headers->get('referer');

        if (!$referer) {
            return $this->redirect($referer);
        }
       
        return $this->redirectToRoute('app_index');
    }

    public function remove(CartService $cartService, int $productId, Request $request)
    {
        $cartService->removeItem($productId);

        $referer = $request->headers->get('referer');

        if (!$referer) {
            return $this->redirect($referer);
        }
       
        return $this->redirectToRoute('app_index');
    }

    public function addQuantity(CartService $cartService, int $cartId)
    {
        $cartService->addQuantity($cartId);

        $cart = $cartService->index();
        $quantities = array_column($cart, 'quantity');

        $totalQuantity = array_sum($quantities);
        $totalPrice = array_sum(array_map(function ($item ) { 
           return $item['quantity'] * floatval($item['price']);
        }, $cart)); 


        return $this->json([
            'success' => 'Item quantity is added', 
            'quantity' => $cart[$cartId]['quantity'],
            'items' => $totalQuantity,
            'itemTotalPrice' => $cart[$cartId]['quantity'] * $cart[$cartId]['price'],
            'totalPrice' => $totalPrice 
        ]);
    }

    public function removeQuantity(CartService $cartService, int $cartId)
    {
        $cartService->removeQuantity($cartId);

        $cart = $cartService->index();
        $quantities = array_column($cart, 'quantity');

        $totalQuantity = array_sum($quantities);
        $totalPrice = array_sum(array_map(function ($item ) { 
           return $item['quantity'] * floatval($item['price']);
        }, $cart)); 

        return $this->json([
            'success' => 'Item quantity is removed',
            'quantity' => $cart[$cartId]['quantity'],
            'items' => $totalQuantity,
            'itemTotalPrice' => $cart[$cartId]['quantity'] * $cart[$cartId]['price'],
            'totalPrice' => $totalPrice    
        ]);
    }
}
