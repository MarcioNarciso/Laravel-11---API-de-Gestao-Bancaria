<?php

namespace App\Interfaces\Services;
use App\Models\BankTransaction;

interface BankTransactionServiceInterface {

    public function execute(BankTransaction $bankTransaction): self;

}