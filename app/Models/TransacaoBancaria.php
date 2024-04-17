<?php

namespace App\Models;

use App\Enums\FormaPagamento;
use App\Traits\UnchangeableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransacaoBancaria extends Model
{
    use SoftDeletes;
    use UnchangeableModel;

    protected $table = 'transacoes_bancarias';

    protected $fillable = [
        'forma_pagamento', 'valor'
    ];

    protected $casts = [
        'forma_pagamento' => FormaPagamento::class
    ];

    public function pagador() : BelongsTo
    {
        return $this->belongsTo(Conta::class, 'pagador_id');
    }

    public function recebedor() : BelongsTo
    {
        return $this->belongsTo(Conta::class, 'recebedor_id');
    }
}
