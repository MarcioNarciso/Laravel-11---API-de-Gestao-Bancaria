<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_verificar_se_uma_conta_existe(): void
    {
        $response = $this->get('/conta?id=1234');

        $response->assertStatus(404);
    }

    public function test_deve_criar_uma_conta_nova_com_saldo_de_500(): void
    {
        $response = $this->postJson('/conta',[
            'conta_id' => 1234,
            'valor' => 500
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'conta_id' => 1234,
                'saldo' => 500.00
            ]);
    }

    public function test_nao_deve_criar_uma_conta_nova_com_saldo_negativo() : void
    {
        $resp = $this->postJson('/conta', [
            'conta_id' => 1100,
            'valor' => -10
        ]);

        $resp->assertStatus(400);
    }

    public function test_nao_deve_criar_uma_conta_nova_com_id_negativo() : void
    {
        $resp = $this->postJson('/conta', [
            'conta_id' => -1100,
            'valor' => 10
        ]);

        $resp->assertStatus(400);
    }
}
