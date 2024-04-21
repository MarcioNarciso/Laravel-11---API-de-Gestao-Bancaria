<?php

namespace App\Models;

use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Factories\RegraCalculoTaxaFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Conta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id', 'saldo'
    ];

    /**
     * Subtrai do saldo da conta o valor que ela está pagando em uma transação.
     * @param float $valor
     * @return $this
     * @throws \App\Exceptions\ContaComSaldoInsuficienteException
     */
    public function subtrairDoSaldo(float $valor) : self
    {
        $isContaComSaldoSuficiente = $this->saldo >= $valor;
    
        if (! $isContaComSaldoSuficiente) {
            throw new ContaComSaldoInsuficienteException("A conta pagadora '{$this->id}' não tem saldo suficiente para completar a transação.");
        }

        $this->saldo -= $valor;

        return $this;
    }

    /**
     * Adiciona ao saldo da conta o valor que ela está recebendo de uma transação.
     * @param float $valor
     * @return $this
     */
    public function adicionarAoSaldo(float $valor) : self
    {
        $this->saldo += $valor;

        return $this;
    }
}
