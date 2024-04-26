<?php

namespace App\Models;

use App\Exceptions\AccountWithInsufficienteBalanceException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends BaseModel
{
    use SoftDeletes;

    /**
     * Define os nomes das colunas "created at", "updated at" e "deleted at".
     */
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $fillable = [
        'id', 'balance'
    ];

    /**
     * Subtrai do saldo da conta o valor que ela está pagando em uma transação.
     * 
     * Se o saldo da conta for insuficiente para a subtração, é lançada uma exceção.
     * 
     * @param   float $value
     * @return  $this
     * 
     * @throws \App\Exceptions\AccountWithInsufficienteBalanceException
     */
    public function subtractFromBalance(float $value) : self
    {
        $isAccountSufficientBalance = $this->balance >= $value;
    
        if (! $isAccountSufficientBalance) {
            throw new AccountWithInsufficienteBalanceException("A conta pagadora '{$this->id}' não tem saldo suficiente para completar a transação.");
        }

        $this->balance -= $value;

        return $this;
    }

    /**
     * Adiciona ao saldo da conta o valor que ela está recebendo em uma transação.
     * 
     * @param   float $value
     * @return  $this
     */
    public function addToBalance(float $value) : self
    {
        $this->balance += $value;

        return $this;
    }
}
