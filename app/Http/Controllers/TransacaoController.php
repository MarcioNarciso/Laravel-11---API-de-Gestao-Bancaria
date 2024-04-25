<?php

namespace App\Http\Controllers;

use App\Enums\FormaPagamento;
use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Http\Resources\ContaResource;
use App\Http\Resources\TransacaoBancariaResource;
use App\Models\Conta;
use App\Models\TransacaoBancaria;
use App\Services\TransacaoBancariaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class TransacaoController extends Controller
{
    public function __construct(
        private TransacaoBancariaService $transacaoService
    ){}

    /**
     * Realiza um transação entre duas contas bancárias.
     */
    #[OA\Post(
        path:"/transacao",
        tags:["Transação"],
        description:'Realizar a movimentação de saldos: subtrai do pagador 
        (junto com a taxa) e adiciona ao receber (sem taxa).',
        requestBody: new OA\RequestBody(
            description:"Informações necessárias para realizar a movimentação de 
            saldo entre as contas.",
            required: true,
            content:[
                new OA\JsonContent(ref:TransacaoBancariaResource::class)
            ]
        ),
        responses: [
            new OA\Response(
                response:201,
                description:"Transação realizada com sucesso. Retorna os dados 
                atualizados da conta do pagador.",
                content: [
                    new OA\JsonContent(ref:ContaResource::class)
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
    public function transacao(Request $req) {
        $dados = $req->input();

        /**
         * Realiza a validação das informações da transação.
         * A forma de pagamento deve ser somente um caractere em maiúsculo.
         */
        $validator = Validator::make($dados, [
            'forma_pagamento' => 'required|size:1|uppercase',
            'pagador_id' => 'required|integer|min:1',
            'recebedor_id' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        $formaDeParamento = FormaPagamento::tryFrom($dados['forma_pagamento']);

        /**
         * Caso a forma de pagamento fornecida seja inválida, retorna o HTTP STATUS 400.
         */
        if (empty($formaDeParamento)) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        $pagador = Conta::find($dados['pagador_id']);
        $recebedor = Conta::find($dados['recebedor_id']);

        /**
         * Caso a conta pagadora e/ou recebedora informada não exista, 
         * retorna o HTTP STATUS 404 para o cliente.
         */
        if (empty($pagador) || empty($recebedor)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        $transacao = new TransacaoBancaria([
            'forma_pagamento' => $formaDeParamento, 
            'valor' => $dados['valor']
        ]);

        $transacao->pagador()->associate($pagador);
        $transacao->recebedor()->associate($recebedor);

        try {

            $this->transacaoService->realizarTransacao($transacao);

        } catch (ContaComSaldoInsuficienteException $e) {
            return response(status: Response::HTTP_NOT_FOUND);
        } catch (RegraCalculoTaxaInexistenteException) {
            /**
             * Caso a forma de pagamento fornecida seja inválida, retorna o HTTP STATUS 400.
             * Forma de pagamento sem classe de cálculo da taxa.
             */
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        return response(new ContaResource($pagador), Response::HTTP_CREATED);
    }

    /**
     * Lista todas as transações de determinada conta.
     */
    #[OA\Get(
        path:"/transacao/{contaId}",
        tags:["Transação"],
        description:'Retorna uma lista com todas as transações de determinada conta.',
        parameters: [
            new OA\Parameter(
                parameter:"contaId",
                name:"contaId", 
                in:"path", 
                required: true,
                description:"ID da conta",
                schema: new OA\Schema(
                    type:"integer"
                )
            ),
        ],
        responses: [
            new OA\Response(
                response:200,
                description:"Uma listagem de todas contas ativas.",
                content: [
                    new OA\JsonContent(ref:TransacaoBancariaResource::class)
                ]
            ),
            new OA\Response(
                response:404,
                description:"A conta requisitada não existe ou foi desativada."
            )
        ]
    )]
    public function listarTransacoes(Conta $conta) {
        $transacoesDaConta = TransacaoBancaria::getTransacoesDaConta($conta);

        return response(TransacaoBancariaResource::collection($transacoesDaConta));
    }
}
