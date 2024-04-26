<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Traits\UnchangeableModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
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

    public function payer() : BelongsTo
    {
        return $this->belongsTo(Account::class, 'payerId');
    }

    public function receiver() : BelongsTo
    {
        return $this->belongsTo(Account::class, 'receiverId');
    }

    public static function getAccountTransactions(Account $account, ?int $perPage = null, ?int $page) : LengthAwarePaginator
    {
        return BankTransaction::where('payerId', $account->id)
                                ->orWhere('receiverId', $account->id)
                                ->paginate($perPage, page: $page);
    }
}
