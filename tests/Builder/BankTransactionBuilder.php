<?php

namespace Tests\Builder;
use App\Enums\PaymentMethod;
use App\Models\Account;
use App\Models\BankTransaction;

/**
 * Um Test Data Builder para facilitar a instanciação da TransacaoBancaria nos
 * testes.
 */
class BankTransactionBuilder
{
    private BankTransaction $bankTransaction;

    public function __construct(PaymentMethod $paymentMethod = null, float $transactionValue = 0)
    {
        $this->bankTransaction = new BankTransaction([
            'paymentMethod' => $paymentMethod, 
            'value' => $transactionValue
        ]);
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod) : self
    {
        $this->bankTransaction->paymentMethod = $paymentMethod;
        return $this;
    }

    public function setTransactionValue(float $transactionValue) : self
    {
        $this->bankTransaction->value = $transactionValue;
        return $this;
    }

    public function setPayer(Account $payer) : self 
    {
        $this->bankTransaction->payer()->associate($payer);
        return $this;
    }

    public function setReceiver(Account $receiver) : self 
    {
        $this->bankTransaction->receiver()->associate($receiver);
        return $this;
    }

    public function build() : BankTransaction
    {
        return $this->bankTransaction;
    }
}
