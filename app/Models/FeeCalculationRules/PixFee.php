<?php

namespace App\Models\FeeCalculationRules;

class PixFee extends FeeCalculationRule
{
    public function getFee() : float
    {
        return 0.0;
    }
}