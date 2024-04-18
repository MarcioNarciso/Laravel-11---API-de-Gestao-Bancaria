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
    private function subtrairDoSaldo(float $valor) : self
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
    private function adicionarAoSaldo(float $valor) : self
    {
        $this->saldo += $valor;

        return $this;
    }

    /**
     * Realiza a transação bancária na conta em questão.
     * Se não for possível realizar a transação, uma exceção é lançada.
     * 
     * @param TransacaoBancaria $transacaoBancaria  Define o tipo de forma de pagamento e o valor transacionado.
     * @return $this
     * @throws \App\Exceptions\ContaComSaldoInsuficienteException
     * @throws \App\Exceptions\RegraCalculoTaxaInexistenteException
     */
    public static function realizarTransacao(TransacaoBancaria $transacaoBancaria) : void
    {
        DB::transaction(function () use ($transacaoBancaria) {

            /**
             * Calcula o valor da transação com base na taxa da forma de pagamento.
             */
            $valorDaTransacao = RegraCalculoTaxaFactory::make($transacaoBancaria->forma_pagamento)
                                    ->calcularValorTaxa($transacaoBancaria->valor);
    
            /**
             * Valor total que deve ser descontado da conta pagadora.
             */
            $valorTotalDaTransacao = $valorDaTransacao + $transacaoBancaria->valor;
    
            /**
             * Subtrai o valor total da transação (taxa + valor da transação) 
             * do saldo do pagador.
             */
            $transacaoBancaria->pagador->subtrairDoSaldo($valorTotalDaTransacao)->save();

            /**
             * Adiciona somente o valor da transação (sem taxa) ao recebedor.
             */
            $transacaoBancaria->recebedor->adicionarAoSaldo($transacaoBancaria->valor)->save();

            /**
             * Salva a transação no banco para histórico.
             */
            $transacaoBancaria->save();

        });
    }
}
