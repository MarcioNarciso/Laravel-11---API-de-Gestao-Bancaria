<?php

namespace App\Enums;

enum FormaPagamento : string{
    case CREDITO = 'C';
    case DEBITO = 'D';
    case PIX = 'P';
}