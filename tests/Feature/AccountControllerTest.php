<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_verificar_se_uma_conta_existe(): void
    {
        $response = $this->get('/accounts/1234');

        $response->assertStatus(404);
    }

    public function test_deve_criar_uma_conta_nova_com_saldo_de_500(): void
    {
        $response = $this->postJson('/accounts',[
            'value' => 500
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'balance' => 500.00
            ]);
    }

    public function test_nao_deve_criar_uma_conta_nova_com_saldo_negativo() : void
    {
        $resp = $this->postJson('/accounts', [
            'accountId' => 1100,
            'value' => -10
        ]);

        $resp->assertStatus(400);
    }
}
