<?php

namespace App\Models\FeeCalculationRules;

abstract class FeeCalculationRule
{
    /**
     * O valor da taxa da operação é calculado sobre o valor absoluto da operação.
     * Ou seja, desconsiderando o sinal.
     * @param float $operationValue  Valor que será transacionado da conta.
     * @return float
     */
    public function calculate(float $operationValue) : float
    {
        if ($operationValue < 0) {
            $operationValue *= -1;
        }

        return $this->getFee() * $operationValue;
    }

    public abstract function getFee() : float;
}