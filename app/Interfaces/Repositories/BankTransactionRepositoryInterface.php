<?php

namespace App\Interfaces\Repositories;
use App\Models\Account;
use App\Models\BankTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BankTransactionRepositoryInterface {

    /**
     * Persiste a transação bancária no banco.
     *
     * Se ocorrer algum erro, uma exceção é lançada.
     * 
     * @return $this
     * @throws \App\Exceptions\ErrorPersistingModelException 
     */
    public function save (BankTransaction $bankTransaction) : self;

    /**
     * Busca todas as transações de determinada conta.
     *
     * @param  Account   $account
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function getAccountTransactions (Account $account, ?int $perPage = null, ?int $page = null) : LengthAwarePaginator;

}