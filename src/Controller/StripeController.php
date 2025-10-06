<?php

namespace App\Controller;

use App\Message\PaymentIntentFailed;
use App\Message\PaymentIntentSuccessful;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

final class StripeController extends AbstractController
{
    private string $whKey;

    public function __construct(    private ParameterBagInterface $parameterBag)
    {
        $this->whKey = $this->getParameter('stripe.webhook_secret');
    }

    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function index(
        Request $request,     
        LoggerInterface $logger,
        MessageBusInterface $bus
        )  : Response
    {      
            $payload = $request->getContent();
            $sig_header = $request->headers->get('Stripe-Signature');
    
            try {
                $event = Webhook::constructEvent($payload, $sig_header, $this->whKey);
            } catch (UnexpectedValueException $e) {
                $logger->error('Unexpected value:  '. $e->getMessage());
                return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
            } catch (SignatureVerificationException $e) {
                $logger->error('Unexpected signature:  '. $e->getMessage());
                return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
            }
    
            match ($event->type) {
                'payment_intent.succeeded' => $bus->dispatch(new PaymentIntentSuccessful($event->data->object)),
                'payment_intent.payment_failed' => $bus->dispatch(new PaymentIntentFailed($event->data->object)),
                default => $logger->error('Unknown event type '. $event->type),
            };
    
            return new Response('Webhook handled', Response::HTTP_OK);
    }
}
