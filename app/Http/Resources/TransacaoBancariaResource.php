<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\formatCurrency;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "Transação Bancária",
    type: "object",
    required: ["recebedor_id", "pagador_id", "forma_pagamento", "valor"],
    properties: [
        new OA\Property(
            property:"recebedor_id",
            title:"recebedor_id",
            type:"integer"
        ),
        new OA\Property(
            property:"pagador_id",
            title:"pagador_id",
            type:"integer"
        ),
        new OA\Property(
            property:"forma_pagamento",
            title:"forma_pagamento",
            type:"string",
            description: "Formas de pagamento aceitas: 'C' (Crédito), 'D' (Débito) e 'P' (Pix)"
        ),
        new OA\Property(
            property:"valor",
            title:"valor",
            type:"number"
        )
    ]
)]
class TransacaoBancariaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'recebedor_id' => $this->recebedor_id,
            'pagador_id' => $this->pagador_id,
            'forma_pagamento' => $this->forma_pagamento,
            'valor' => formatCurrency($this->valor),
            'realizada_em' => $this->created_at
        ];
    }
}
