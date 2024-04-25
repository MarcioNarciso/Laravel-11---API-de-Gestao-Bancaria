<?php 

namespace App\Services;
use App\Factories\FeeCalculationRuleFactory;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;

class BankTransactionService
{

    /**
     * Injeta as dependências da service pelo Service Container.
     */
    public function __construct(
        private FeeCalculationRuleFactory $feeCalculationRuleFactory
    ){}

    /**
     * Realiza a transação bancária na conta em questão.
     * Se não for possível realizar a transação, uma exceção é lançada.
     * 
     * @param BankTransaction $bankTransaction  Define o tipo de forma de pagamento, o valor transacionado e as contas.
     * @throws \App\Exceptions\AccountWithInsufficienteBalanceException
     * @throws \App\Exceptions\NonExistFeeCalculcationRuleException
     */
    public function execute(BankTransaction $bankTransaction) : void
    {
        DB::transaction(function () use ($bankTransaction) {

            /**
             * Calcula o valor da transação com base na taxa da forma de pagamento.
             */
            $transactionValue = $this->feeCalculationRuleFactory
                                     ->make($bankTransaction->paymentMethod)
                                     ->calculate($bankTransaction->value);
    
            /**
             * Valor total que deve ser descontado da conta pagadora.
             */
            $totalTransactionAmount = $transactionValue + $bankTransaction->value;
    
            /**
             * Subtrai o valor total da transação (taxa + valor da transação) 
             * do saldo do pagador.
             */
            $bankTransaction->payer->subtractFromBalance($totalTransactionAmount)->save();

            /**
             * Adiciona somente o valor da transação (sem taxa) ao recebedor.
             */
            $bankTransaction->receiver->addToBalance($bankTransaction->value)->save();

            /**
             * Salva a transação no banco para histórico.
             */
            $bankTransaction->save();

        });
    }

}
