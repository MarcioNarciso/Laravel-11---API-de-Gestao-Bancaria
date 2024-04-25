<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\formatCurrency;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "Conta",
    type: "object",
    required: ["saldo"],
    properties: [
        new OA\Property(
            property:"contaId",
            title:"ID da conta",
            type:"integer"
        ),
        new OA\Property(
            property:"saldo",
            title:"Saldo da conta",
            type:"number"
        )
    ]
)]
class ContaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'contaId' => $this->id,
            'saldo' => formatCurrency($this->saldo),
            'criadaEm' => $this->createdAt
        ];
    }
}
