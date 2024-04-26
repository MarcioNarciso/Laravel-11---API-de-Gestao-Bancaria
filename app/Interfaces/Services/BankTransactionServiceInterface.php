<?php

namespace App\Interfaces\Services;
use App\Models\BankTransaction;

/**
 * Interface que define o serviço de transações bancárias.
 */
interface BankTransactionServiceInterface {

    /**
     * Realiza a transação bancária na conta em questão.
     * 
     * Se não for possível realizar a transação, uma exceção é lançada.
     * 
     * @param   \App\Models\BankTransaction $bankTransaction  Define o tipo de forma de pagamento, o valor transacionado e as contas.
     * @return  $this
     * 
     * @throws \App\Exceptions\AccountWithInsufficienteBalanceException
     * @throws \App\Exceptions\NonExistFeeCalculcationRuleException
     * @throws \App\Exceptions\ErrorPersistingModelException
     */
    public function execute(BankTransaction $bankTransaction): self;

}