<?php

namespace Tests\Builder;
use App\Enums\PaymentMethod;
use App\Models\Account;
use App\Models\BankTransaction;


class BankTransactionDirector
{
    public function __construct(
        private BankTransactionBuilder $builder
    ) 
    {}

    public function build(PaymentMethod $paymentMethod = PaymentMethod::PIX, 
                          float $transactionValue = 10.0) : BankTransaction
    {
        $this->builder
                ->setPaymentMethod($paymentMethod)
                ->setTransactionValue($transactionValue)
                ->setPayer(new Account(['id' => 1, 'balance' => 100]))
                ->setReceiver(new Account(['id' => 2, 'balance' => 100]));

        return $this->builder->build();
    }
}
