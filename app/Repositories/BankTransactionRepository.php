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

    /**
     * Busca todas as transações que um conta está relacionada, seja como pagadora
     * ou recebedora.
     * 
     * @param   \App\Models\Account $account
     * @param   null|int            $perPage    Quantidade de registros por página.
     * @param   null|int            $page       Número da página de registros.
     * @return  \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAccountTransactions (Account $account, ?int $perPage = null, ?int $page = null) : LengthAwarePaginator
    {
        return BankTransaction::where('payerId', $account->id)
                                ->orWhere('receiverId', $account->id)
                                ->paginate($perPage, page: $page);
    }
}
