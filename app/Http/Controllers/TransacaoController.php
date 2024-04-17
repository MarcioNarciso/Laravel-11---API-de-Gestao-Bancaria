<?php

namespace App\Http\Controllers;

use App\Enums\FormaPagamento;
use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Http\Resources\ContaResource;
use App\Models\Conta;
use App\Models\Transacao;
use App\Models\TransacaoBancaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransacaoController extends Controller
{
    /**
     * Realiza a transação sobre determinada conta bancária.
     */
    public function transacao(Request $req) {
        $dados = $req->input();

        /**
         * Realiza a validação das informações da transação.
         * A forma de pagamento deve ser somente um caractere em maiúsculo.
         */
        $validator = Validator::make($dados, [
            'forma_pagamento' => 'required|size:1|uppercase',
            'conta_id' => 'required',
            'valor' => 'required|numeric|min:1'
        ]);

        /**
         * Caso as informações da conta não sejam válidas, retorna o HTTP STATUS 400.
         */
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

        $conta = Conta::find($dados['conta_id']);

        /**
         * Caso a conta informada não exista, retorna o HTTP STATUS 404 para o cliente.
         */
        if (empty($conta)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        try {

            $conta->realizarTransacao(new TransacaoBancaria($formaDeParamento, $dados['valor']));

        } catch (ContaComSaldoInsuficienteException $e) {
            return response(status: Response::HTTP_NOT_FOUND);
        } catch (Exception) {
            /**
             * Caso a forma de pagamento fornecida seja inválida, retorna o HTTP STATUS 400.
             * Forma de pagamento sem classe de cálculo da taxa.
             */
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        /**
         * Persiste a conta com o novo saldo.
         */
        $conta->save();

        return response(new ContaResource($conta), Response::HTTP_CREATED);
    }
}
