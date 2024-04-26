<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\formatCurrency;

use OpenApi\Attributes as OA;

/**
 * Classe que representa o recurso "Conta" enviado para o cliente.
 */
#[OA\Schema(
    title: "Account",
    type: "object",
    required: ["balance"],
    properties: [
        new OA\Property(
            property:"accountId",
            title:"ID da conta",
            type:"string"
        ),
        new OA\Property(
            property:"balance",
            title:"Saldo da conta",
            type:"number"
        ),
        new OA\Property(
            property:"createdAt",
            title:"Data de criação da conta",
            type:"string"
        )
    ]
)]
class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'accountId' => $this->id,
            'balance' => formatCurrency($this->balance),
            'createdAt' => $this->createdAt
        ];
    }
}
