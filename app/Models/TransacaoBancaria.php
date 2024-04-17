<?php

namespace App\Models;

use App\Enums\FormaPagamento;

readonly class TransacaoBancaria
{
    public function __construct(
        public FormaPagamento $formaPagamento,
        public float $valor = 0.0
    )
    {}
}
