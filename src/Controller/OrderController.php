<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Http\FormRequests\StoreOrderRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\CartService;
use App\Entity\Order;
use App\Entity\OrderLines;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;


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
    public function index(StoreOrderRequest $request, EntityManagerInterface $entityManager): Response
    {
            $cart = $this->cartService->index();
    
            try {
                $stripe = new StripeClient($this->apiKey);
    
                $totalPrice = array_sum(array_map(function ($item) {
                    return $item['quantity'] * floatval($item['price']);
                }, $cart));     


                $data = $request->validated();
    
                $order = new Order();
                $order->setFullName($data['full_name']);
                $order->setEmail($data['email']);
                $order->setAddress($data['address']);
                $order->setPostCode($data['postcode']);
                $order->setCity($data['city']);

                $entityManager->persist($order);

                
                foreach ($cart as $item) {
                    $orderLines = new OrderLines();
                    $orderLines->setName($item['name']);
                    $orderLines->setQuantity($item['quantity']);
                    $orderLines->setPrice($item['price']);
                    
                    $order->addOrderLine($orderLines);
                    $entityManager->persist($orderLines);
                }

                $entityManager->flush();    

    
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
                return $this->redirectToRoute('order_callback_failed', ['error' => $e->getMessage()]);
            }
    }

    #[Route('/order/success', name: 'app_order_success')]
    public function callbackSuccess(CartService $cartService) : Response
    {
        $cartService->removeAll();

        return $this->render('order/success.html.twig');
    }

    #[Route('/order/failed', name: 'app_order_failed')]
    public function callbackFailed() : Response
    {
        return $this->render('order/failed.html.twig');
    }
}
