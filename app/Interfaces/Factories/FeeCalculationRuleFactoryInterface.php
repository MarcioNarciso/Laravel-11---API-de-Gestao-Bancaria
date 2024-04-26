<?php

namespace App\Interfaces\Factories;

use App\Enums\PaymentMethod;
use App\Models\FeeCalculationRules\FeeCalculationRule;

/**
 * Interface que define a fábrica de regras de cálculo das taxas com base na
 * forma de pagamento.
 */
interface FeeCalculationRuleFactoryInterface {

    /**
     * Instancia a classe da regra de cálculo da taxa com base na forma de pagamento.
     * 
     * Se a forma de pagamento não tiver uma regra definida, é lançada uma exceção.
     * 
     * @param   \App\Enums\PaymentMethod $paymentMethod
     * @return  \App\Models\FeeCalculationRules\FeeCalculationRule
     * 
     * @throws \App\Exceptions\NonExistFeeCalculcationRuleException
     */
    public function make(PaymentMethod $paymentMethod): FeeCalculationRule;

}