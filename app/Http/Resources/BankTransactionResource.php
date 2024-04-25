<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\formatCurrency;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: "Bank Transaction",
    type: "object",
    required: ["receiverId", "payerId", "paymentMethod", "value"],
    properties: [
        new OA\Property(
            property:"receiverId",
            title:"ID da conta do recebedor",
            type:"string"
        ),
        new OA\Property(
            property:"payerId",
            title:"ID da conta do pagador",
            type:"string"
        ),
        new OA\Property(
            property:"paymentMethod",
            title:"Meio que o pagamento será realizado.",
            type:"string",
            description: "Formas de pagamento aceitas: 'C' (Crédito), 'D' (Débito) e 'P' (Pix)"
        ),
        new OA\Property(
            property:"value",
            title:"Valor da transação",
            type:"number"
        )
    ]
)]
class BankTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'receiverId' => $this->receiverId,
            'payerId' => $this->payerId,
            'paymentMethod' => $this->paymentMethod,
            'value' => formatCurrency($this->value),
            'date' => $this->createdAt
        ];
    }
}
