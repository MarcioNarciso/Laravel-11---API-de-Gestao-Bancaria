<?php

namespace Tests\Unit;

use App\Enums\FormaPagamento;
use App\Models\Conta;
use App\Models\TransacaoBancaria;
use PHPUnit\Framework\TestCase;

class ContaTest extends TestCase
{
    private Conta $conta;

    #[\Override]
    protected function setUp(): void
    {
        $this->conta = new Conta([
            'id' => 1,
            'saldo' => 1000
        ]);
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_debito(): void
    {
        $transacao = new TransacaoBancaria(FormaPagamento::DEBITO, 100.0);

        $this->conta->realizarTransacao($transacao);

        $this->assertEquals((1000 - (100 + (100 * 0.3))), $this->conta->saldo);
    }

    public function test_deve_efetuar_pagamento_via_cartao_de_credito(): void
    {
        $transacao = new TransacaoBancaria(FormaPagamento::CREDITO, 100.0);

        $this->conta->realizarTransacao($transacao);

        $this->assertEquals((1000 - (100 + (100 * 0.5))), $this->conta->saldo);
    }

    public function test_deve_efetuar_pagamento_via_pix(): void
    {
        $transacao = new TransacaoBancaria(FormaPagamento::PIX, 100.0);

        $this->conta->realizarTransacao($transacao);

        $this->assertEquals((1000 - (100 + (100 * 0.0))), $this->conta->saldo);
    }

    public function test_deve_zerar_o_saldo_da_conta() : void
    {
        $transacao = new TransacaoBancaria(FormaPagamento::PIX, 1000.0);

        $this->conta->realizarTransacao($transacao);

        $this->assertEquals(0, $this->conta->saldo);
    }

    public function test_saldo_insuficiente_deve_lancar_excecao() : void
    {
        $this->expectException(\Exception::class);

        $transacao = new TransacaoBancaria(FormaPagamento::CREDITO, 1000.0);

        $this->conta->realizarTransacao($transacao);
    }
}
