<?php

namespace App\MessageHandler;

use App\Entity\Order;
use App\Enums\OrderStatus;
use App\Message\PaymentIntentSuccessful;
use App\Repository\OrderRepository;
use App\Repository\OrderLinesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PaymentIntentSuccessfulHandler
{
    public function __construct(private OrderRepository $orderRepository, private OrderLinesRepository $orderLinesRepository)
    {
        
    }


    public function __invoke(PaymentIntentSuccessful $message,  LoggerInterface $logger, EntityManagerInterface $entityManager): void
    {
        $intent = $message->stripeObject;

        $orderId = $intent->metadata->order_id;

        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            $logger->error('Order not found');
        }

        $orderLines = $this->orderLinesRepository->findByExampleField($orderId);

        $order->setStatus(OrderStatus::Failed->value);

        $entityManager->persist($order);
        $entityManager->flush();
    }
}
