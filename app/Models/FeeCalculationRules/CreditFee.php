<?php

namespace App\Models\FeeCalculationRules;

class CreditFee extends FeeCalculationRule
{
    public function getFee() : float
    {
        return 0.05;
    }
}
