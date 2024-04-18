<?php

namespace Tests\Builder;
use App\Enums\FormaPagamento;
use App\Models\Conta;
use App\Models\TransacaoBancaria;


class TransacaoBancariaDirector
{
    public function __construct(
        private TransacaoBancariaBuilder $builder
    ) 
    {}

    public function buildTransacao(FormaPagamento $formaPagamento = FormaPagamento::PIX, 
                                   float $valorDaTransacao = 10.0) : TransacaoBancaria
    {
        $this->builder
                ->setFormaPagamento($formaPagamento)
                ->setValorTransacao($valorDaTransacao)
                ->setPagador(new Conta(['id' => 1, 'saldo' => 100]))
                ->setRecebedor(new Conta(['id' => 2, 'saldo' => 100]));

        return $this->builder->build();
    }
}
