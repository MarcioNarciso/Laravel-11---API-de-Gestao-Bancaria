<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class AccountController extends Controller
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository
    ){}

    /**
     * Endpoint para consultar contas.
     */
    #[OA\Get(
        path:"/accounts",
        tags:["Contas"],
        description:'Retorna uma lista paginada de todas as contas cadastradas,',
        responses: [
            new OA\Response(
                response:200,
                description:"Lista todas contas ativas.",
                content: [
                    new OA\JsonContent(ref:AccountResource::class)
                ]
            )
        ]
    )]
    public function index(Request $request)
    {
        $paginatedAccounts = $this->accountRepository->paginate(5);

        return AccountResource::collection($paginatedAccounts);
    }

    /**
     * Busca determinada conta pelo ID e retorna para o cliente.
     */
    #[OA\Get(
        path:"/accounts/{id}",
        tags:["Contas"],
        description:'Retorna informações da conta específicada pelo parâmetro 
        "id" no path.',
        parameters: [
            new OA\Parameter(
                parameter:"id",
                name:"id", 
                in:"path", 
                description:"ID da conta",
                schema: new OA\Schema(
                    type:"string"
                )
            ),
        ],
        responses: [
            new OA\Response(
                response:200,
                description:"Informações de determinada conta.",
                content: [
                    new OA\JsonContent(ref:AccountResource::class)
                ]
            ),
            new OA\Response(
                response:404,
                description:"Foi requisitada uma determinada conta, mas ela não existe ou está inativa."
            )
        ]
    )]
    public function show(string $id)
    {
        $account = $this->accountRepository->findById($id);

        if (empty($account)) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        /**
         * A conta existe e ela é retornada para o cliente.
         */
        return response(new AccountResource($account));
    }

    /**
     * Armazena a nova conta no banco de dados.
     */
    #[OA\Post(
        path:"/accounts",
        tags:["Contas"],
        description:'Cadastra uma nova conta para transações futuras.',
        requestBody: new OA\RequestBody(
            description:"'value' é o saldo inicial da conta. Deve ser um valor 
            maior que zero.",
            required: true,
            content:[
                new OA\JsonContent(properties: [
                    new OA\Property(
                        property:"value",
                        title:"Saldo inicial da conta.",
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
                    new OA\JsonContent(ref:AccountResource::class)
                ]
            ),
            new OA\Response(
                response:400,
                description:"Informações inválidas da conta."
            ),
            new OA\Response(
                response: 500,
                description:"Erro ao persistir a nova conta. Conta não persistida."
            )
        ]
    )]
    public function store(Request $request)
    {
        $account = $request->input();

        /**
         * Realiza a validação das informações da nova conta.
         * 
         * A conta deve ter o saldo inicial positivo.
         */
        $validator = Validator::make($account, [
            'value' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response(status: Response::HTTP_BAD_REQUEST);
        }

        $account = new Account([
            'balance' => $account['value']
        ]);

        try {
            $this->accountRepository->save($account);
        } catch (\Exception) {
            return response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(new AccountResource($account), Response::HTTP_CREATED);
    }

    /**
     * Desativa determinada conta pelo ID.
     */
    #[OA\Delete(
        path:"/accounts/{id}",
        tags:["Contas"],
        parameters: [
            new OA\Parameter(
                parameter:"id",
                name:"id", 
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
                description:"Conta desativada com sucesso.",
            ),
            new OA\Response(
                response:404,
                description:"Foi requisitada uma determinada conta, mas ela não existe ou ja estava inativa."
            )
        ]
    )]
    public function destroy(string $id)
    {
        if (empty($this->accountRepository->deleteById($id))) {
            return response(status: Response::HTTP_NOT_FOUND);
        }

        return response(status: Response::HTTP_OK);
    }
}
