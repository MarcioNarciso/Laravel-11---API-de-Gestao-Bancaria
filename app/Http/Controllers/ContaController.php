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
     * Retorna informações da conta específicada pelo parâmetro "id" na query 
     * string ou, se o ID não for fornecido, retorna uma listagem de todas
     * as contas cadastradas.
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

        return $this->show($contaId);
    }

    /**
     * Busca determinada conta pelo ID e retorna para o cliente.
     */
    private function show(int $id)
    {
        $conta = Conta::find($id);

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

        if ($validator->fails()) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        /**
         * Verifica se já existe uma conta já cadastrada com o mesmo ID.
         */
        if (! empty($conta['conta_id'])) {
            $contaExistente = Conta::find($conta['conta_id']);
            $hasContaExistente = ! empty($contaExistente);
    
            if ($hasContaExistente) {
                return response(status: Response::HTTP_CONFLICT);
            }
        }

        //
        $conta = Conta::create([
            'id' => $conta['conta_id'] ?? null,
            'saldo' => $conta['valor']
        ]);

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
