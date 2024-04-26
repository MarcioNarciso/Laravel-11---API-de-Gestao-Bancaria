<?php

namespace App\Models\FeeCalculationRules;

/**
 * Classe que define a taxa sobre a forma de pagamento "Crédito".
 */
class CreditFee extends FeeCalculationRule
{
    public function getFee() : float
    {
        return 0.05;
    }
}
