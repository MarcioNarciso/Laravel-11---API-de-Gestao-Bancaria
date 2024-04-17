<?php

namespace App\Models\RegrasCalculoTaxas;
use App\Models\RegrasCalculoTaxas\RegraCalculoTaxa;

class TaxaDoCredito extends RegraCalculoTaxa
{
    public function getTaxa() : float
    {
        return 0.5;
    }
}
