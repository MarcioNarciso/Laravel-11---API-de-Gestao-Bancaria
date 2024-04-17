<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContaResource;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ContaController extends Controller
{
    /**
     * Retorna informações da conta específicada pelo parâmetro "id" na query string.
     */
    public function index(Request $request)
    {
        $contaId = $request->query('id');

        /**
         * Se o parâmetro "id" não existir, retorna todas as contas cadastradas.
         */
        if (empty($contaId)) {
            return response(ContaResource::collection(Conta::all()));
        }

        $conta = Conta::find($contaId);

        /**
         * Se a conta requisitada não existir, é retornado o HTTP STATUS 404.
         */
        if (empty($conta)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        /**
         * A conta existe e ela é retornada para o cliente.
         */
        return response(new ContaResource($conta));
    }

    /**
     * Armazena a nova conta no banco de dados.
     */
    public function store(Request $request)
    {
        $conta = $request->input();

        /**
         * Realiza a validação das informações da nova conta.
         * O ID da conta não é obrigatório, mas se existir, deve ser inteiro.
         */
        $validator = Validator::make($conta, [
            'conta_id' => 'nullable|integer|min:1',
            'valor' => 'required|numeric|min:0'
        ]);

        /**
         * Caso as informações da conta não sejam válidas, retorna o HTTP STATUS 400.
         */
        if ($validator->fails()) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        /**
         * Verifica se já existe uma conta com o ID já cadastrada.
         */
        if (! empty($conta['conta_id'])) {
            
            $contaExistente = Conta::find($conta['conta_id']);
    
            if (! empty($contaExistente)) {
                /**
                 * Se a conta já existir, retorna o HTTP STATUS 409 para o cliente.
                 */
                return response(status: Response::HTTP_CONFLICT);
            }
        }

        //
        $conta = Conta::create([
            'id' => $conta['conta_id'] ?? null,
            'saldo' => $conta['valor']
        ]);

        /**
         * Retorna a nova conta com o HTTP STATUS 201.
         */
        return response(new ContaResource($conta), Response::HTTP_CREATED);
    }

    /**
     * Desativa determinada conta pelo ID.
     */
    public function destroy(string $id)
    {
        $conta = Conta::find($id);

        if (empty($conta)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        $conta->delete();

        return response(status: Response::HTTP_OK);
    }
}
