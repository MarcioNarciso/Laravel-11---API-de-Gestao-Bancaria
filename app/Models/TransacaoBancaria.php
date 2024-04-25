<?php

namespace App\Models;

use App\Enums\FormaPagamento;
use App\Traits\UnchangeableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransacaoBancaria extends BaseModel
{
    use SoftDeletes;
    use UnchangeableModel;

    /**
     * Define os nomes das colunas "created at", "updated at" e "deleted at".
     */
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $table = 'transacoes_bancarias';

    protected $fillable = [
        'formaPagamento', 'valor'
    ];

    protected $casts = [
        'formaPagamento' => FormaPagamento::class
    ];

    public function pagador() : BelongsTo
    {
        return $this->belongsTo(Conta::class, 'pagadorId');
    }

    public function recebedor() : BelongsTo
    {
        return $this->belongsTo(Conta::class, 'recebedorId');
    }

    public static function getTransacoesDaConta(Conta $conta)
    {
        return TransacaoBancaria::where('pagadorId', $conta->id)
                                ->orWhere('recebedorId', $conta->id)
                                ->get();
    }
}
