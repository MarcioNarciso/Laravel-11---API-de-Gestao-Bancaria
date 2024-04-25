<?php

namespace App\Models\FeeCalculationRules;

class DebitFee extends FeeCalculationRule
{
    public function getFee() : float
    {
        return round(3 / 100, 2);
    }
}