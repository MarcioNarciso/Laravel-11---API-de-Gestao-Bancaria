<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Exceptions\ContaComSaldoInsuficienteException;
use App\Services\BankTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Builder\TransacaoBancariaBuilder;
use Tests\Builder\TransacaoBancariaDirector;
use Tests\TestCase;


class TransacaoBancariaServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransacaoBancariaDirector $transacaoBancariaDirector;
    private BankTransactionService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->transacaoBancariaDirector = new TransacaoBancariaDirector(new TransacaoBancariaBuilder());
        $this->service = $this->app->make(BankTransactionService::class);
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_debito(): void
    {
        // Arrange
        $valorTransacao = 10.0;
        $transacao = $this->transacaoBancariaDirector->buildTransacao(PaymentMethod::DEBIT);

        // Act
        $this->service->execute($transacao);

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
        $transacao = $this->transacaoBancariaDirector->buildTransacao(PaymentMethod::CREDIT);

        // Act
        $this->service->execute($transacao);

        // Assert
        $this->assertEquals((100 - ($valorTransacao + ($valorTransacao * 0.05))), 
                            $transacao->pagador->saldo);
        $this->assertEquals(100 + $valorTransacao, 
                            $transacao->recebedor->saldo);
    }

    public function test_deve_efetuar_transferencia_via_pix(): void
    {
        // Arrange
        $valorTransacao = 10.0;
        $transacao = $this->transacaoBancariaDirector->buildTransacao(PaymentMethod::PIX);

        // Act
        $this->service->execute($transacao);

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
        $transacao = $this->transacaoBancariaDirector->buildTransacao(PaymentMethod::PIX, 
                                                                      $valorTransacao);

        // Act
        $this->service->execute($transacao);

        // Assert
        $this->assertEquals(0, $transacao->pagador->saldo);
        $this->assertEquals(200, $transacao->recebedor->saldo);
    }

    public function test_saldo_insuficiente_deve_lancar_excecao() : void
    {
        // Assert
        $this->expectException(ContaComSaldoInsuficienteException::class);

        // Arrange
        $transacao = $this->transacaoBancariaDirector->buildTransacao(PaymentMethod::CREDIT, 1000.0);

        // Act
        $this->service->execute($transacao);
    }

    public function test_ao_lancar_excecao_os_saldos_das_contas_devem_ficar_inalterados() : void
    {
        // Arrange
        $transacao = $this->transacaoBancariaDirector->buildTransacao(PaymentMethod::CREDIT, 100.0);

        // Act
        try {
            $this->service->execute($transacao);
        } catch (ContaComSaldoInsuficienteException) {
        }

        // Assert
        $this->assertEquals(100, $transacao->pagador->saldo);
        $this->assertEquals(100, $transacao->recebedor->saldo);
    }
}
