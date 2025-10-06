<?php

namespace App\Message;

final class PaymentIntentSuccessful
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     public function __construct(public readonly object $stripeObject)
    {
    }
}
