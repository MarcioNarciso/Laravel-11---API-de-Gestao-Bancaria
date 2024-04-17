<?php

namespace App\Models\RegrasCalculoTaxas;
use App\Models\RegrasCalculoTaxas\RegraCalculoTaxa;

class TaxaDoDebito extends RegraCalculoTaxa
{
    public function getTaxa() : float
    {
        return round(3 / 100, 2);
    }
}