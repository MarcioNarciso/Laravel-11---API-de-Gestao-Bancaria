<?php

namespace App\Models\RegrasCalculoTaxas;

abstract class RegraCalculoTaxa
{
    /**
     * O valor da taxa da operação é calculado sobre o valor absoluto da operação.
     * Ou seja, desconsiderando o sinal.
     * @param float $valorOperacao  Valor que será transacionado da conta.
     * @return float
     */
    public function calcularValorTaxa(float $valorOperacao) : float
    {
        if ($valorOperacao < 0) {
            $valorOperacao *= -1;
        }

        return $this->getTaxa() * $valorOperacao;
    }

    public abstract function getTaxa() : float;
}