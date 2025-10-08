<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;
use App\Services\CartService;

class CartExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private CartService $cartService)
    {
        // Inject dependencies if needed
    }

    public function getCartTotal()
    {
        $cart = $this->cartService->index();
        $quantities = array_column($cart, 'quantity');
        return array_sum($quantities);
    }
}
