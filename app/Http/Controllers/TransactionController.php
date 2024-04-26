<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\AccountWithInsufficienteBalanceException;
use App\Exceptions\NonExistFeeCalculcationRuleException;
use App\Http\Resources\AccountResource;
use App\Http\Resources\BankTransactionResource;
use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Interfaces\Repositories\BankTransactionRepositoryInterface;
use App\Interfaces\Services\BankTransactionServiceInterface;
use App\Models\BankTransaction;
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
        private BankTransactionServiceInterface $bankTransactionService,
        private BankTransactionRepositoryInterface $bankTransactionRepository,
        private AccountRepositoryInterface $accountRepository
    ){}

    /**
     * Endpoint para realizar uma transação entre duas contas bancárias.
     */
    #[OA\Post(
        path:"/transactions",
        tags:["Transações"],
        description:'Realiza a movimentação de saldos: subtrai do pagador 
        (junto com a taxa) e adiciona ao recebedor (sem taxa).',
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
                description:"Informações inválidas da transação ou o saldo do 
                pagador é insuficiente para realizar a transação."
            ),
            new OA\Response(
                response:404,
                description:"A conta do recebedor e/ou do pagador não foi encontrada."
            ),
            new OA\Response(
                response:500,
                description:"Erro ao persistir a transação."
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
        $payer = $this->accountRepository->findById($data['payerId']);
        $receiver = $this->accountRepository->findById($data['receiverId']);

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

        /**
         * Associa as contas pagadora e recebedora à transação.
         */
        $transaction->payer()->associate($payer);
        $transaction->receiver()->associate($receiver);

        try {

            /**
             * Realiza a transação.
             */
            $this->bankTransactionService->execute($transaction);

        } catch (AccountWithInsufficienteBalanceException | NonExistFeeCalculcationRuleException) {
            /**
             * Caso a forma de pagamento fornecida seja inválida, responde com o 
             * HTTP STATUS 400: Forma de pagamento sem classe de cálculo da taxa.
             * 
             * Caso a conta pagador não tenha saldo suficiente, responde com o 
             * HTTP STATUS 400.
             */
            return response(status: Response::HTTP_BAD_REQUEST);
        } catch (ErrorPersistingModelException) {
            /**
             * Caso ocorra um erro ao persistir a transação, responde com o 
             * HTTP STATUS 500.
             */
            return response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(new AccountResource($payer), Response::HTTP_CREATED);
    }

    /**
     * Endpoint para Listar todas as transações de determinada conta.
     */
    #[OA\Get(
        path:"/transactions/{accountId}",
        tags:["Transações"],
        description:'Retorna uma lista paginada com todas as transações de 
        determinada conta.',
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
                description:"Lista paginada de todas transações de uma conta.",
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
        $account = $this->accountRepository->findById($accountId);

        if (empty($account)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        /**
         * Realiza a busca paginada das transações de determinada conta, com 
         * cinco registros por página.
         */
        $accountTransactions = $this->bankTransactionRepository->getAccountTransactions($account, 5);

        return BankTransactionResource::collection($accountTransactions);
    }
}
