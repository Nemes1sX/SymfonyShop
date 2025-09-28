<?php

namespace App\Enums;

enum OrderStatus : int {
    case Failed = 0;
    case AwaitingPayment = 1;
    case Complete = 2;
}