<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Arrange
        $this->postJson('/accounts',[
            'value' => 500.0
        ]);

        $this->postJson('/accounts', [
            'value' => 500.0
        ]);
    }

    public function test_deve_consultar_saldo_de_uma_conta(): void
    {
        // Act
        $resp = $this->get('/accounts/1');

        // Assert
        $resp
            ->assertStatus(200)
            ->assertJson([
                'accountId' => 1,
                'balance' => 500.00
            ]);
    }

    public function test_deve_efetuar_compra_no_valor_de_50_no_debito(): void
    {
        // Arrange
        $accountBalance = 500.0;
        $transactionValue = 50.0;

        // Act
        $resp = $this->postJson('/transactions', [
            'paymentMethod' => 'D',
            'payerId' => 1,
            'receiverId' => 2,
            'value' => $transactionValue
        ]);

        // Assert
        $resp
            ->assertStatus(201)
            ->assertJson([
                'accountId' => 1,
                'balance' => $accountBalance - ($transactionValue + ($transactionValue * 0.03))
            ]);
    }

    public function test_deve_efetuar_compra_no_valor_de_100_no_credito(): void
    {
        $accountBalance = 500.0;
        $transactionValue = 100.0;

        // Act
        $resp = $this->postJson('/transactions', [
            'paymentMethod' => 'C',
            'payerId' => 1,
            'receiverId' => 2,
            'value' => $transactionValue
        ]);

        // Assert
        $resp
            ->assertStatus(201)
            ->assertJson([
                'accountId' => 1,
                'balance' => $accountBalance - ($transactionValue + ($transactionValue * 0.05))
            ]);
    }

    public function test_deve_realizar_uma_transferencia_no_valor_de_75_no_pix(): void
    {
        $accountBalance = 500.0;
        $transactionValue = 75.00;

        // Act
        $resp = $this->postJson('/transactions', [
            'paymentMethod' => 'P',
            'payerId' => 1,
            'receiverId' => 2,
            'value' => $transactionValue
        ]);

        // Assert
        $resp
            ->assertStatus(201)
            ->assertJson([
                'accountId' => 1,
                'balance' => $accountBalance - $transactionValue
            ]);
    }

    public function test_nao_deve_realizar_uma_transferencia_para_formas_de_pagamento_desconhecidas(): void
    {
        // Act
        $resp = $this->postJson('/transactions', [
            'paymentMethod' => 'Y',
            'payerId' => 1,
            'receiverId' => 2,
            'value' => 75.00
        ]);

        // Assert
        $resp->assertStatus(400);
    }

    public function test_nao_deve_realizar_transferencia_com_valor_negativo(): void
    {
        // Act
        $resp = $this->postJson('/transactions', [
            'paymentMethod' => 'P',
            'payerId' => 1,
            'receiverId' => 2,
            'value' => -75.00
        ]);

        // Assert
        $resp->assertStatus(400);
    }

    public function test_deve_listar_as_transacoes_da_conta() : void
    {
        // Arrange
        $resp = $this->postJson('/transactions', [
            'paymentMethod' => 'P',
            'payerId' => 1,
            'receiverId' => 2,
            'value' => 50
        ]);

        $resp->assertStatus(201);

        // Act
        $resp = $this->getJson('/transactions/1');

        // Assert
        $resp
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 1)->etc());
    }
}
