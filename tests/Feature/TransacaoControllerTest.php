<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Arrange
        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => 500.0
        ]);

        $this->postJson('/conta', [
            'conta_id' => 1235,
            'valor' => 500.0
        ]);
    }

    public function test_deve_consultar_saldo_de_uma_conta(): void
    {
        // Act
        $resp = $this->get('/conta?id=1234');

        // Assert
        $resp
            ->assertStatus(200)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => 500.00
            ]);
    }

    public function test_deve_efetuar_compra_no_valor_de_50_no_debito(): void
    {
        // Arrange
        $saldoDaConta = 500.0;
        $valorDaTransacao = 50.0;

        // Act
        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'D',
            'pagador_id' => 1234,
            'recebedor_id' => 1235,
            'valor' => $valorDaTransacao
        ]);

        // Assert
        $resp
            ->assertStatus(201)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => $saldoDaConta - ($valorDaTransacao + ($valorDaTransacao * 0.03))
            ]);
    }

    public function test_deve_efetuar_compra_no_valor_de_100_no_credito(): void
    {
        $saldoDaConta = 500.0;
        $valorDaTransacao = 100.0;

        // Act
        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'C',
            'pagador_id' => 1234,
            'recebedor_id' => 1235,
            'valor' => $valorDaTransacao
        ]);

        // Assert
        $resp
            ->assertStatus(201)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => $saldoDaConta - ($valorDaTransacao + ($valorDaTransacao * 0.05))
            ]);
    }

    public function test_deve_realizar_uma_transferencia_no_valor_de_75_no_pix(): void
    {
        $saldoDaConta = 500.0;
        $valorDaTransacao = 75.00;

        // Act
        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'P',
            'pagador_id' => 1234,
            'recebedor_id' => 1235,
            'valor' => $valorDaTransacao
        ]);

        // Assert
        $resp
            ->assertStatus(201)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => $saldoDaConta - $valorDaTransacao
            ]);
    }

    public function test_nao_deve_realizar_uma_transferencia_para_formas_de_pagamento_desconhecidas(): void
    {
        // Act
        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'Y',
            'pagador_id' => 1234,
            'recebedor_id' => 1235,
            'valor' => 75.00
        ]);

        // Assert
        $resp->assertStatus(400);
    }

    public function test_nao_deve_realizar_transferencia_com_valor_negativo(): void
    {
        // Act
        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'P',
            'pagador_id' => 1234,
            'recebedor_id' => 1235,
            'valor' => -75.00
        ]);

        // Assert
        $resp->assertStatus(400);
    }
}
