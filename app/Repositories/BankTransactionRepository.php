<?php

namespace App\Repositories;
use App\Exceptions\ErrorPersistingModelException;
use App\Interfaces\Repositories\BankTransactionRepositoryInterface;
use App\Models\Account;
use App\Models\BankTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BankTransactionRepository implements BankTransactionRepositoryInterface
{
    public function save (BankTransaction $bankTransaction) : self
    {
        $isTransactionSaved = false;

        try {
            $isTransactionSaved = $bankTransaction->save();
        } catch (\Exception) {
            throw new ErrorPersistingModelException("Não foi possível persistir a transação bancária.");
        }

        if (! $isTransactionSaved) {
            throw new ErrorPersistingModelException("Não foi possível persistir a transação bancária.");
        }

        return $this;
    }

    public function getAccountTransactions (Account $account, ?int $perPage = null, ?int $page = null) : LengthAwarePaginator
    {
        return BankTransaction::getAccountTransactions($account, $perPage, $page);
    }
}
