<?php

namespace App\Interfaces\Factories;

use App\Enums\PaymentMethod;
use App\Models\FeeCalculationRules\FeeCalculationRule;


interface FeeCalculationRuleFactoryInterface {

    public function make(PaymentMethod $paymentMethod): FeeCalculationRule;

}