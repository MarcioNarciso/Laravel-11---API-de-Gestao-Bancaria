<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_consultar_saldo_de_uma_conta(): void
    {
        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => 500.0
        ]);

        $resp = $this->get('/conta?id=1234');

        $resp
            ->assertStatus(200)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => 500.00
            ]);
    }

    public function test_deve_efetuar_compra_no_valor_de_50_no_debito(): void
    {
        $saldoDaConta = 500.0;

        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => $saldoDaConta
        ]);

        $valorDaTransacao = 50.0;

        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'D',
            'conta_id' => 1234,
            'valor' => $valorDaTransacao
        ]);

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

        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => $saldoDaConta
        ]);

        $valorDaTransacao = 100.0;

        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'C',
            'conta_id' => 1234,
            'valor' => $valorDaTransacao
        ]);

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

        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => $saldoDaConta
        ]);

        $valorDaTransacao = 75.00;

        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'P',
            'conta_id' => 1234,
            'valor' => $valorDaTransacao
        ]);

        $resp
            ->assertStatus(201)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => $saldoDaConta - $valorDaTransacao
            ]);
    }

    public function test_nao_deve_realizar_uma_transferencia_para_formas_de_pagamento_desconhecidas(): void
    {
        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => 500.0
        ]);

        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'Y',
            'conta_id' => 1234,
            'valor' => 75.00
        ]);

        $resp->assertStatus(400);
    }

    public function test_nao_deve_realizar_transferencia_com_valor_negativo(): void
    {
        $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => 500.0
        ]);

        $resp = $this->postJson('/transacao', [
            'forma_pagamento' => 'P',
            'conta_id' => 1234,
            'valor' => -75.00
        ]);

        $resp->assertStatus(400);
    }
}
