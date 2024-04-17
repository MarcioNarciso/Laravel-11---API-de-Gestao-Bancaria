<?php

namespace App\Models;

use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Factories\RegraCalculoTaxaFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    protected $fillable = [
        'id', 'saldo'
    ];

    /**
     * Realiza a transação bancária na conta em questão.
     * Se não for possível realizar a transação, uma exceção é lançada.
     * 
     * @param TransacaoBancaria $transacaoBancaria  Define o tipo de forma de pagamento e o valor transacionado.
     * @return $this
     * @throws \Exception
     */
    public function realizarTransacao(TransacaoBancaria $transacaoBancaria) : self
    {
        /**
         * Calcula o valor da transação com base na taxa da forma de pagamento.
         */
        $valorDaTransacao = RegraCalculoTaxaFactory::make($transacaoBancaria->formaPagamento)
                                ->calcularValorTaxa($transacaoBancaria->valor);

        /**
         * Valor total que deve ser descontado da conta.
         */
        $valorTotalDaTransacao = $valorDaTransacao + $transacaoBancaria->valor;

        $hasContaSaldoSuficiente = $this->saldo >= $valorTotalDaTransacao;

        /**
         * Se a conta não tiver saldo suficiente para concluir a transação,
         * é lançada uma exceção.
         */
        if (! $hasContaSaldoSuficiente) {
            throw new ContaComSaldoInsuficienteException("A conta '{$this->id}' não tem saldo suficiente para completar a transação.");
        }

        $this->saldo = $this->saldo - $valorTotalDaTransacao;

        return $this;
    }
}
