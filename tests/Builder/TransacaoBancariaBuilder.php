<?php

namespace Tests\Builder;
use App\Enums\PaymentMethod;
use App\Models\Account;
use App\Models\BankTransaction;

/**
 * Um Test Data Builder para facilitar a instanciação da TransacaoBancaria nos
 * testes.
 */
class TransacaoBancariaBuilder
{
    private BankTransaction $transacaoBancaria;

    public function __construct(PaymentMethod $formaPagamento = null, float $valorTransacao = 0)
    {
        $this->transacaoBancaria = new BankTransaction([
            'forma_pagamento' => $formaPagamento, 
            'valor' => $valorTransacao
        ]);
    }

    public function setFormaPagamento(PaymentMethod $formaPagamento) : self
    {
        $this->transacaoBancaria->forma_pagamento = $formaPagamento;
        return $this;
    }

    public function setValorTransacao(float $valorTransacao) : self
    {
        $this->transacaoBancaria->valor = $valorTransacao;
        return $this;
    }

    public function setPagador(Account $pagador) : self 
    {
        $this->transacaoBancaria->pagador()->associate($pagador);
        return $this;
    }

    public function setRecebedor(Account $recebedor) : self 
    {
        $this->transacaoBancaria->recebedor()->associate($recebedor);
        return $this;
    }

    public function build() : BankTransaction
    {
        return $this->transacaoBancaria;
    }
}
