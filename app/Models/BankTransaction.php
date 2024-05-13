<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Traits\UnchangeableModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends BaseModel
{
    use SoftDeletes;
    use UnchangeableModel;

    /**
     * Define os nomes das colunas "created at", "updated at" e "deleted at".
     */
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $table = 'bank_transactions';

    protected $fillable = [
        'paymentMethod', 'value'
    ];

    protected $casts = [
        'paymentMethod' => PaymentMethod::class
    ];

    /**
     * Define um relacionamento um-para-muitos com a conta.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payer() : BelongsTo
    {
        return $this->belongsTo(Account::class, 'payerId');
    }

    /**
     * Define um relacionamento um-para-muitos com a conta.
     * 
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver() : BelongsTo
    {
        return $this->belongsTo(Account::class, 'receiverId');
    }

}
