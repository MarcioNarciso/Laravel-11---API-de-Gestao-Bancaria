<?php

namespace Tests\Builder;
use App\Enums\FormaPagamento;
use App\Models\Conta;
use App\Models\TransacaoBancaria;

/**
 * Um Test Data Builder para facilitar a instanciação da TransacaoBancaria nos
 * testes.
 */
class TransacaoBancariaBuilder
{
    private TransacaoBancaria $transacaoBancaria;

    public function __construct(FormaPagamento $formaPagamento = null, float $valorTransacao = 0)
    {
        $this->transacaoBancaria = new TransacaoBancaria([
            'forma_pagamento' => $formaPagamento, 
            'valor' => $valorTransacao
        ]);
    }

    public function setFormaPagamento(FormaPagamento $formaPagamento) : self
    {
        $this->transacaoBancaria->forma_pagamento = $formaPagamento;
        return $this;
    }

    public function setValorTransacao(float $valorTransacao) : self
    {
        $this->transacaoBancaria->valor = $valorTransacao;
        return $this;
    }

    public function setPagador(Conta $pagador) : self 
    {
        $this->transacaoBancaria->pagador()->associate($pagador);
        return $this;
    }

    public function setRecebedor(Conta $recebedor) : self 
    {
        $this->transacaoBancaria->recebedor()->associate($recebedor);
        return $this;
    }

    public function build() : TransacaoBancaria
    {
        return $this->transacaoBancaria;
    }
}
