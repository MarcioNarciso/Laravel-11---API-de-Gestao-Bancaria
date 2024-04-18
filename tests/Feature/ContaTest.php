<?php

namespace Tests\Feature;

use App\Enums\FormaPagamento;
use App\Models\Conta;
use App\Exceptions\ContaComSaldoInsuficienteException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Builder\TransacaoBancariaBuilder;
use Tests\Builder\TransacaoBancariaDirector;
use Tests\TestCase;


class ContaTest extends TestCase
{
    use RefreshDatabase;

    private TransacaoBancariaDirector $transacaoBancariaDirector;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->transacaoBancariaDirector = new TransacaoBancariaDirector(new TransacaoBancariaBuilder());
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_debito(): void
    {
        // Arrange
        $valorTransacao = 10.0;
        $transacao = $this->transacaoBancariaDirector->buildTransacao(FormaPagamento::DEBITO);

        // Act
        Conta::realizarTransacao($transacao);

        // Assert
        $this->assertEquals((100 - ($valorTransacao + ($valorTransacao * 0.03))), 
                            $transacao->pagador->saldo);
        $this->assertEquals(100 + $valorTransacao, 
                            $transacao->recebedor->saldo);
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_credito(): void
    {
        // Arrange
        $valorTransacao = 10.0;
        $transacao = $this->transacaoBancariaDirector->buildTransacao(FormaPagamento::CREDITO);

        // Act
        Conta::realizarTransacao($transacao);

        // Assert
        $this->assertEquals((100 - ($valorTransacao + ($valorTransacao * 0.05))), 
                            $transacao->pagador->saldo);
        $this->assertEquals(100 + $valorTransacao, 
                            $transacao->recebedor->saldo);
    }

    public function test_deve_efetuar_pagamento_via_pix(): void
    {
        // Arrange
        $valorTransacao = 10.0;
        $transacao = $this->transacaoBancariaDirector->buildTransacao(FormaPagamento::PIX);

        // Act
        Conta::realizarTransacao($transacao);

        // Assert
        $this->assertEquals((100 - ($valorTransacao + ($valorTransacao * 0.0))), 
                            $transacao->pagador->saldo);
        $this->assertEquals(100 + $valorTransacao, 
                            $transacao->recebedor->saldo);
    }

    public function test_deve_zerar_o_saldo_da_conta() : void
    {
        // Arrange
        $valorTransacao = 100.0;
        $transacao = $this->transacaoBancariaDirector->buildTransacao(FormaPagamento::PIX, 
                                                                      $valorTransacao);

        // Act
        Conta::realizarTransacao($transacao);

        // Assert
        $this->assertEquals(0, $transacao->pagador->saldo);
        $this->assertEquals(200, $transacao->recebedor->saldo);
    }

    public function test_saldo_insuficiente_deve_lancar_excecao() : void
    {
        // Assert
        $this->expectException(ContaComSaldoInsuficienteException::class);

        // Arrange
        $transacao = $this->transacaoBancariaDirector->buildTransacao(FormaPagamento::CREDITO, 1000.0);

        // Act
        Conta::realizarTransacao($transacao);
    }
}
