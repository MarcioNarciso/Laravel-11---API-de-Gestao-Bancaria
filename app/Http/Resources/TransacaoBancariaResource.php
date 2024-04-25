<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\formatCurrency;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "Transação Bancária",
    type: "object",
    required: ["recebedorId", "pagadorId", "formaPagamento", "valor"],
    properties: [
        new OA\Property(
            property:"recebedorId",
            title:"ID da conta do recebedor",
            type:"integer"
        ),
        new OA\Property(
            property:"pagadorId",
            title:"ID da conta do pagador",
            type:"integer"
        ),
        new OA\Property(
            property:"formaPagamento",
            title:"Meio que o pagamento foi realizado.",
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
            'recebedorId' => $this->recebedorId,
            'pagadorId' => $this->pagadorId,
            'formaPagamento' => $this->formaPagamento,
            'valor' => formatCurrency($this->valor),
            'realizadaEm' => $this->createdAt
        ];
    }
}
