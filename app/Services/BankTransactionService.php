<?php 

namespace App\Services;
use App\Interfaces\Factories\FeeCalculationRuleFactoryInterface;
use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Interfaces\Repositories\BankTransactionRepositoryInterface;
use App\Interfaces\Services\BankTransactionServiceInterface;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;

class BankTransactionService implements BankTransactionServiceInterface
{

    /**
     * Injeta as dependências da service pelo Service Container.
     */
    public function __construct(
        private FeeCalculationRuleFactoryInterface $feeCalculationRuleFactory,
        private AccountRepositoryInterface $accountRepository,
        private BankTransactionRepositoryInterface $bankTransactionRepository
    ){}

    /**
     * Realiza a transação bancária na conta em questão.
     * 
     * Se não for possível realizar a transação, uma exceção é lançada.
     * 
     * @param BankTransaction $bankTransaction  Define o tipo de forma de pagamento, o valor transacionado e as contas.
     * @throws \App\Exceptions\AccountWithInsufficienteBalanceException
     * @throws \App\Exceptions\NonExistFeeCalculcationRuleException
     * @throws \App\Exceptions\ErrorPersistingModelException
     */
    public function execute(BankTransaction $bankTransaction) : self
    {
        DB::transaction(function () use ($bankTransaction) {

            /**
             * Instancia a regra de cálculo da taxa conforme a forma de pagamento 
             * e calcula o valor da taxa sobre a transação.
             */
            $feeValue = $this->feeCalculationRuleFactory->make($bankTransaction->paymentMethod)
                                                        ->calculate($bankTransaction->value);
    
            /**
             * Calcula o valor total que deve ser descontado do pagador.
             */
            $totalTransactionValue = $feeValue + $bankTransaction->value;
    
            /**
             * Subtrai o valor total da transação (taxa + valor da transação) 
             * do saldo do pagador.
             */
            $bankTransaction->payer->subtractFromBalance($totalTransactionValue);

            /**
             * Adiciona somente o valor da transação (sem taxa) ao saldo do recebedor.
             */
            $bankTransaction->receiver->addToBalance($bankTransaction->value);

            /**
             * Salva as contas com os saldos atualizados.
             */
            $this->accountRepository->save($bankTransaction->payer)
                                    ->save($bankTransaction->receiver);

            /**
             * Salva a transação no banco para histórico.
             */
            $this->bankTransactionRepository->save($bankTransaction);

        });

        return $this;
    }

}
