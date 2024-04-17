<?php

namespace App\Models\RegrasCalculoTaxas;
use App\Models\RegrasCalculoTaxas\RegraCalculoTaxa;

class TaxaDoPix extends RegraCalculoTaxa
{
    public function getTaxa() : float
    {
        return 0.0;
    }
}