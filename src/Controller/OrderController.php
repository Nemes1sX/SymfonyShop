<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Http\FormRequests\StoreOrderRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\CartService;
use App\Entity\Order;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


final class OrderController extends AbstractController
{
    private string $apiKey;

    public function __construct(
        private CartService $cartService,
        private ParameterBagInterface $parameterBag,
        private LoggerInterface $logger,
    ) {
        $this->apiKey = $this->getParameter('stripe.api_key');
    }

    #[Route('/order', name: 'app_order')]
    public function index(StoreOrderRequest $request): Response
    {
            $cart = $this->cartService->index();
    
            try {
                $stripe = new StripeClient($this->apiKey);
    
                $totalPrice = array_sum(array_map(function ($item) {
                    return $item['quantity'] * floatval($item['price']);
                }, $cart));     
    
                $order = new Order();
                $order->setFullName($request->getPayload()->full_name);
                $order->setEmail($request->getPayLoad()->email);
                $order->setAddress($request->address);
                $order->setPostCode($request->postcode);
                $order->setCity($request->city);
                
                foreach ($cart as $item) {
                    $order->orderLines()->create([
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
    
                $metadata = [
                    'order_id' => (string) $order->getId(),
                    'order_status' => (string) $order->getStatus(),
                    'fullname' => (string) $order->getFullName(),
                    'email' => (string) $order->getEmail(),
                    'postcode' => (string) $order->getPostCode(),
                    'address' => (string) $order->getAddress(),
                    'city' => (string) $order->getCity(),
                ];
    
                $response = $stripe->checkout->sessions->create([
                    'success_url' => $this->generateUrl('order_callback_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url'  => $this->generateUrl('order_callback_failed', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'metadata' => $metadata,
                    'payment_intent_data' => [
                        'metadata' => $metadata,
                    ],
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'EUR',
                                'unit_amount' => $totalPrice * 100,
                                'product_data' => [
                                    'name' => 'No.' . $order->getId()
                                ]
                            ],
                            'quantity' => 1
                        ]
                    ],
                    'mode' => 'payment',
                ]);
    
                return $this->redirect($response->url);
            } catch (ApiErrorException $e) {
                $this->logger->error('Log error:'. $e->getMessage());
                return $this->redirectToRoute('order.callback.failed', ['error' => $e->getMessage()]);
            }
    }
}
