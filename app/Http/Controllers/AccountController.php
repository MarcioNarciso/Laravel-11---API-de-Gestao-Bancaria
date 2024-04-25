<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContaResource;
use App\Models\Account;
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
        description:'Retorna uma lista paginada de todas as contas cadastradas,',
        responses: [
            new OA\Response(
                response:200,
                description:"Lista todas contas ativas.",
                content: [
                    new OA\JsonContent(ref:ContaResource::class)
                ]
            )
        ]
    )]
    public function index(Request $request)
    {
        $contasPaginadas = Account::paginate(5);
        return ContaResource::collection($contasPaginadas);
    }

    /**
     * Busca determinada conta pelo ID e retorna para o cliente.
     */
    #[OA\Get(
        path:"/accounts/{id}",
        tags:["Accounts"],
        description:'Retorna informações da conta específicada pelo parâmetro 
        "id" no path.',
        parameters: [
            new OA\Parameter(
                parameter:"id",
                name:"id", 
                in:"path", 
                description:"ID da conta",
                schema: new OA\Schema(
                    type:"integer"
                )
            ),
        ],
        responses: [
            new OA\Response(
                response:200,
                description:"Informações de determinada conta.",
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
    public function show(Account $conta)
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
            description:"Informações sobre a conta no formato JSON. 'valor' 
            é o saldo inicial da conta.",
            required: true,
            content:[
                new OA\JsonContent(properties: [
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
            )
        ]
    )]
    public function store(Request $request)
    {
        $conta = $request->input();

        /**
         * Realiza a validação das informações da nova conta.
         */
        $validator = Validator::make($conta, [
            'valor' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        $conta = Account::create([
            'saldo' => $conta['valor']
        ]);

        return response(new ContaResource($conta), Response::HTTP_CREATED);
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
    public function destroy(Account $conta)
    {
        $conta->delete();

        return response(status: Response::HTTP_OK);
    }
}
