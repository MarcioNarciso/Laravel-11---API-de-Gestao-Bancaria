<?php

namespace App\Enums;

enum PaymentMethod : string{
    case CREDIT = 'C';
    case DEBIT = 'D';
    case PIX = 'P';
}