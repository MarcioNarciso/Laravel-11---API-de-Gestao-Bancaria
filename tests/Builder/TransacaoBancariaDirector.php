<?php

namespace Tests\Builder;
use App\Enums\PaymentMethod;
use App\Models\Account;
use App\Models\BankTransaction;


class TransacaoBancariaDirector
{
    public function __construct(
        private TransacaoBancariaBuilder $builder
    ) 
    {}

    public function buildTransacao(PaymentMethod $formaPagamento = PaymentMethod::PIX, 
                                   float $valorDaTransacao = 10.0) : BankTransaction
    {
        $this->builder
                ->setFormaPagamento($formaPagamento)
                ->setValorTransacao($valorDaTransacao)
                ->setPagador(new Account(['id' => 1, 'saldo' => 100]))
                ->setRecebedor(new Account(['id' => 2, 'saldo' => 100]));

        return $this->builder->build();
    }
}
