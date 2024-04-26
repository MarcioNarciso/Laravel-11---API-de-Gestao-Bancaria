<?php

namespace App\Models\FeeCalculationRules;

/**
 * Classe que define a taxa sobre a forma de pagamento "Débito".
 */
class DebitFee extends FeeCalculationRule
{
    public function getFee() : float
    {
        return round(3 / 100, 2);
    }
}