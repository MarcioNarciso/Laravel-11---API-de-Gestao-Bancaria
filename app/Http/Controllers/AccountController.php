<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContaResource;
use App\Models\Conta;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class AccountController extends Controller
{
    /**
     * Endpoint para consultar contas.
     */
    #[OA\Get(
        path:"/accounts",
        tags:["Accounts"],
        description:'Retorna informações da conta específicada pelo parâmetro 
        "id" na query string ou, se o ID for omitido, retorna uma listagem de 
        todas as contas cadastradas',
        parameters: [
            new OA\Parameter(
                parameter:"id",
                name:"id", 
                in:"query", 
                description:"ID da conta",
                schema: new OA\Schema(
                    type:"integer"
                )
            ),
        ],
        responses: [
            new OA\Response(
                response:200,
                description:"Informações de determinada conta ou uma listagem de todas contas ativas.",
                content: [
                    new OA\JsonContent(ref:ContaResource::class)
                ]
            ),
            new OA\Response(
                response:404,
                description:"Foi requisitada uma determinada conta, mas ela não existe ou está inativa."
            )
        ]
    )]
    public function index(Request $request)
    {
        return response(ContaResource::collection(Conta::all()));
    }

    /**
     * Busca determinada conta pelo ID e retorna para o cliente.
     */
    public function show(Conta $conta)
    {
        /**
         * A conta existe e ela é retornada para o cliente.
         */
        return response(new ContaResource($conta));
    }

    /**
     * Armazena a nova conta no banco de dados.
     */
    #[OA\Post(
        path:"/conta",
        tags:["Conta"],
        description:'Cadastra uma nova conta para transações futuras.',
        requestBody: new OA\RequestBody(
            description:"Informações sobre a conta no formato JSON. 'conta_id' 
            é opcional e, se for omitido, será geradado um ID automático. 'valor' 
            é o saldo inicial da conta.",
            required: true,
            content:[
                new OA\JsonContent(properties: [
                    new OA\Property(
                        property:"conta_id",
                        title:"conta_id",
                        description:"ID da conta (opcional)",
                        type: "integer"
                    ),
                    new OA\Property(
                        property:"valor",
                        title:"valor",
                        description:"Saldo inicial da conta.",
                        type: "number"
                    )
                ])
            ]
        ),
        responses: [
            new OA\Response(
                response:201,
                description:"Conta criada com sucesso.",
                content: [
                    new OA\JsonContent(ref:ContaResource::class)
                ]
            ),
            new OA\Response(
                response:400,
                description:"Informações inválidas da conta."
            ),
            new OA\Response(
                response:409,
                description:"Já existe uma conta cadastrada com o mesmo ID."
            )
        ]
    )]
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

        try {
            $conta = Conta::create([
                'id' => $conta['conta_id'] ?? null,
                'saldo' => $conta['valor']
            ]);
    
            return response(new ContaResource($conta), Response::HTTP_CREATED);
        } catch (UniqueConstraintViolationException) {
            return response(status: Response::HTTP_CONFLICT);
        }
    }

    /**
     * Desativa determinada conta pelo ID.
     */
    #[OA\Delete(
        path:"/conta/{id}",
        tags:["Conta"],
        parameters: [
            new OA\Parameter(
                parameter:"id",
                name:"id", 
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
                description:"Conta desativada com sucesso.",
            ),
            new OA\Response(
                response:404,
                description:"Foi requisitada uma determinada conta, mas ela não existe ou ja estava inativa."
            )
        ]
    )]
    public function destroy(Conta $contum)
    {
        $contum->delete();

        return response(status: Response::HTTP_OK);
    }
}
