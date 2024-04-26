<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Exceptions\AccountWithInsufficienteBalanceException;
use App\Services\BankTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Builder\BankTransactionBuilder;
use Tests\Builder\BankTransactionDirector;
use Tests\TestCase;


class BankTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private BankTransactionDirector $bankTransactionDirector;
    private BankTransactionService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->bankTransactionDirector = new BankTransactionDirector(new BankTransactionBuilder());
        $this->service = $this->app->make(BankTransactionService::class);
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_debito(): void
    {
        // Arrange
        $transactionValue = 10.0;
        $bankTransaction = $this->bankTransactionDirector->build(PaymentMethod::DEBIT);

        // Act
        $this->service->execute($bankTransaction);

        // Assert
        $this->assertEquals((100 - ($transactionValue + ($transactionValue * 0.03))), 
                            $bankTransaction->payer->balance);
        $this->assertEquals(100 + $transactionValue, 
                            $bankTransaction->receiver->balance);
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_credito(): void
    {
        // Arrange
        $transactionValue = 10.0;
        $bankTransaction = $this->bankTransactionDirector->build(PaymentMethod::CREDIT);

        // Act
        $this->service->execute($bankTransaction);

        // Assert
        $this->assertEquals((100 - ($transactionValue + ($transactionValue * 0.05))), 
                            $bankTransaction->payer->balance);
        $this->assertEquals(100 + $transactionValue, 
                            $bankTransaction->receiver->balance);
    }

    public function test_deve_efetuar_transferencia_via_pix(): void
    {
        // Arrange
        $transactionValue = 10.0;
        $bankTransaction = $this->bankTransactionDirector->build(PaymentMethod::PIX);

        // Act
        $this->service->execute($bankTransaction);

        // Assert
        $this->assertEquals((100 - ($transactionValue + ($transactionValue * 0.0))), 
                            $bankTransaction->payer->balance);
        $this->assertEquals(100 + $transactionValue, 
                            $bankTransaction->receiver->balance);
    }

    public function test_deve_zerar_o_saldo_da_conta() : void
    {
        // Arrange
        $transactionValue = 100.0;
        $bankTransaction = $this->bankTransactionDirector->build(PaymentMethod::PIX, 
                                                                      $transactionValue);

        // Act
        $this->service->execute($bankTransaction);

        // Assert
        $this->assertEquals(0, $bankTransaction->payer->balance);
        $this->assertEquals(200, $bankTransaction->receiver->balance);
    }

    public function test_saldo_insuficiente_deve_lancar_excecao() : void
    {
        // Assert
        $this->expectException(AccountWithInsufficienteBalanceException::class);

        // Arrange
        $bankTransaction = $this->bankTransactionDirector->build(PaymentMethod::CREDIT, 1000.0);

        // Act
        $this->service->execute($bankTransaction);
    }

    public function test_ao_lancar_excecao_os_saldos_das_contas_devem_ficar_inalterados() : void
    {
        // Arrange
        $bankTransaction = $this->bankTransactionDirector->build(PaymentMethod::CREDIT, 100.0);

        // Act
        try {
            $this->service->execute($bankTransaction);
        } catch (AccountWithInsufficienteBalanceException) {
        }

        // Assert
        $this->assertEquals(100, $bankTransaction->payer->balance);
        $this->assertEquals(100, $bankTransaction->receiver->balance);
    }
}
