<?php 

namespace App\Services;
use App\Factories\RegraCalculoTaxaFactory;
use App\Models\TransacaoBancaria;
use Illuminate\Support\Facades\DB;

class TransacaoBancariaService
{
    public function __construct(
        private RegraCalculoTaxaFactory $regraCalculoTaxaFactory
    ){}

    /**
     * Realiza a transação bancária na conta em questão.
     * Se não for possível realizar a transação, uma exceção é lançada.
     * 
     * @param TransacaoBancaria $transacaoBancaria  Define o tipo de forma de pagamento, o valor transacionado e as contas.
     * @return $this
     * @throws \App\Exceptions\ContaComSaldoInsuficienteException
     * @throws \App\Exceptions\RegraCalculoTaxaInexistenteException
     */
    public function realizarTransacao(TransacaoBancaria $transacaoBancaria) : void
    {
        DB::transaction(function () use ($transacaoBancaria) {

            /**
             * Calcula o valor da transação com base na taxa da forma de pagamento.
             */
            $valorDaTransacao = $this->regraCalculoTaxaFactory->make($transacaoBancaria->forma_pagamento)
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