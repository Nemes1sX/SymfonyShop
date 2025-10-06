<?php

namespace App\MessageHandler;

use App\Message\PaymentIntentFailed;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Enums\OrderStatus;
use App\Repository\OrderLinesRepository;

#[AsMessageHandler]
final class PaymentIntentFailedHandler
{
    public function __construct(private OrderRepository $orderRepository)
    {
        
    }

    public function __invoke(PaymentIntentFailed $message, LoggerInterface $logger, EntityManagerInterface $entityManager): void
    {
        $intent = $message->stripeObject;

        $orderId = $intent->metadata->order_id;

        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            $logger->error('Order not found');
        }

        
        $order->setStatus(OrderStatus::Failed->value);

        $entityManager->persist($order);
        $entityManager->flush();
    }
}
