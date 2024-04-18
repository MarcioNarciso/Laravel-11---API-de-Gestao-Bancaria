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
            property:"conta_id",
            title:"conta_id",
            type:"integer"
        ),
        new OA\Property(
            property:"saldo",
            title:"saldo",
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
            'conta_id' => $this->id,
            'saldo' => formatCurrency($this->saldo),
            'criada_em' => $this->created_at
        ];
    }
}
