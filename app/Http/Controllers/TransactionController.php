<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\AccountWithInsufficienteBalanceException;
use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Exceptions\NonExistFeeCalculcationRuleException;
use App\Http\Resources\AccountResource;
use App\Http\Resources\BankTransactionResource;
use App\Models\Account;
use App\Models\BankTransaction;
use App\Services\BankTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{
    /**
     * Injeta as dependências da controller pelo Service Container.
     */
    public function __construct(
        private BankTransactionService $bankTransactionService
    ){}

    /**
     * Realiza um transação entre duas contas bancárias.
     */
    #[OA\Post(
        path:"/transactions",
        tags:["Transações"],
        description:'Realizar a movimentação de saldos: subtrai do pagador 
        (junto com a taxa) e adiciona ao receber (sem taxa).',
        requestBody: new OA\RequestBody(
            description:"Informações necessárias para realizar a movimentação de 
            saldo entre as contas.",
            required: true,
            content:[
                new OA\JsonContent(ref:BankTransactionResource::class)
            ]
        ),
        responses: [
            new OA\Response(
                response:201,
                description:"Transação realizada com sucesso. Retorna os dados 
                atualizados da conta do pagador.",
                content: [
                    new OA\JsonContent(ref:AccountResource::class)
                ]
            ),
            new OA\Response(
                response:400,
                description:"Informações inválidas da transação."
            ),
            new OA\Response(
                response:404,
                description:"A conta do recebedor e/ou do pagador não foi encontrada
                ou o saldo do pagador é insuficiente para realizar a transação."
            )
        ]
    )]
    public function transaction(Request $req) {
        $data = $req->input();

        /**
         * Realiza a validação das informações da transação.
         * A forma de pagamento deve ser somente um caractere em maiúsculo.
         */
        $validator = Validator::make($data, [
            'paymentMethod' => 'required|size:1|uppercase',
            'payerId' => 'required|min:1',
            'receiverId' => 'required|min:1',
            'value' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        /**
         * Caso a forma de pagamento fornecida seja inválida, retorna o HTTP STATUS 400.
         */
        $paymentMethod = PaymentMethod::tryFrom($data['paymentMethod']);

        if (empty($paymentMethod)) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        /**
         * Caso a conta pagadora e/ou recebedora informada não exista, 
         * retorna o HTTP STATUS 404 para o cliente.
         */
        $payer = Account::find($data['payerId']);
        $receiver = Account::find($data['receiverId']);

        if (empty($payer) || empty($receiver)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        /**
         * Instancia a transação.
         */
        $transaction = new BankTransaction([
            'paymentMethod' => $paymentMethod, 
            'value' => $data['value']
        ]);

        $transaction->payer()->associate($payer);
        $transaction->receiver()->associate($receiver);

        try {

            $this->bankTransactionService->execute($transaction);

        } catch (AccountWithInsufficienteBalanceException $e) {
            return response(status: Response::HTTP_NOT_FOUND);
        } catch (NonExistFeeCalculcationRuleException) {
            /**
             * Caso a forma de pagamento fornecida seja inválida, retorna o HTTP STATUS 400.
             * Forma de pagamento sem classe de cálculo da taxa.
             */
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        return response(new AccountResource($payer), Response::HTTP_CREATED);
    }

    /**
     * Lista todas as transações de determinada conta.
     */
    #[OA\Get(
        path:"/transactions/{accountId}",
        tags:["Transações"],
        description:'Retorna uma lista com todas as transações de determinada conta.',
        parameters: [
            new OA\Parameter(
                parameter:"accountId",
                name:"accountId", 
                in:"path", 
                required: true,
                description:"ID da conta",
                schema: new OA\Schema(
                    type:"string"
                )
            ),
        ],
        responses: [
            new OA\Response(
                response:200,
                description:"Uma listagem de todas transações de uma conta.",
                content: [
                    new OA\JsonContent(ref:BankTransactionResource::class)
                ]
            ),
            new OA\Response(
                response:404,
                description:"A conta requisitada não existe ou foi desativada."
            )
        ]
    )]
    public function listTransactions(string $accountId) {
        $account = Account::find($accountId);

        if (empty($account)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        $accountTransactions = BankTransaction::getAccountTransactions($account);

        return response(BankTransactionResource::collection($accountTransactions));
    }
}
