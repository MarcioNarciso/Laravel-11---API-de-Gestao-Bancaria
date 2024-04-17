<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function App\Helpers\formatCurrency;

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
