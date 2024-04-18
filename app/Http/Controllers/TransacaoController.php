<?php

namespace App\Http\Controllers;

use App\Enums\FormaPagamento;
use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Http\Resources\ContaResource;
use App\Http\Resources\TransacaoBancariaResource;
use App\Models\Conta;
use App\Models\TransacaoBancaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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
            'pagador_id' => 'required|integer|min:1',
            'recebedor_id' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:1'
        ]);

        /**
         * Caso as informações da transferência não sejam válidas, retorna o HTTP STATUS 400.
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

        $pagador = Conta::find($dados['pagador_id']);
        $recebedor = Conta::find($dados['recebedor_id']);

        /**
         * Caso a conta pagadora ou recebedora informada não existam, 
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

            Conta::realizarTransacao($transacao);

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

    public function listarTransacoes(int $contaId) {
        
        $conta = Conta::find($contaId);

        if (empty($conta)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        $transacoesDaConta = TransacaoBancaria::where('pagador_id', $contaId)
                                                ->orWhere('recebedor_id', $contaId)
                                                ->get();

        return response(TransacaoBancariaResource::collection($transacoesDaConta));
    }
}
