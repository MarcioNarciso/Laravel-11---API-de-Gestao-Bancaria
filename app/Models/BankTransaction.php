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

    /**
     * Busca todas as transações que um conta está relacionada, seja como pagadora
     * ou recebedora.
     * 
     * @param   \App\Models\Account $account
     * @param   null|int            $perPage    Quantidade de registros por página.
     * @param   null|int            $page       Número da página de registros.
     * @return  \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getAccountTransactions(Account $account, ?int $perPage = null, ?int $page) : LengthAwarePaginator
    {
        return BankTransaction::where('payerId', $account->id)
                                ->orWhere('receiverId', $account->id)
                                ->paginate($perPage, page: $page);
    }
}
