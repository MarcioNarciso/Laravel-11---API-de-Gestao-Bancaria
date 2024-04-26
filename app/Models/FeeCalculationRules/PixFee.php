<?php

namespace App\Models\FeeCalculationRules;

/**
 * Classe que define a taxa sobre a forma de pagamento "Pix".
 */
class PixFee extends FeeCalculationRule
{
    public function getFee() : float
    {
        return 0.0;
    }
}