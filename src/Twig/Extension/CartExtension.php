<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CartExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CartExtension extends AbstractExtension
{
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cart_total', [CartExtensionRuntime::class, 'getCartTotal']),
        ];
    }
}