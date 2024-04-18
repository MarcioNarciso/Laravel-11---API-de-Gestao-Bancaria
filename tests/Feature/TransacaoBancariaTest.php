<?php

namespace Tests\Feature;

use App\Models\TransacaoBancaria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Builder\TransacaoBancariaBuilder;
use Tests\Builder\TransacaoBancariaDirector;
use Tests\TestCase;

class TransacaoBancariaTest extends TestCase
{
    use RefreshDatabase;

    private TransacaoBancariaDirector $transacaoBancariaDirector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transacaoBancariaDirector = new TransacaoBancariaDirector(new TransacaoBancariaBuilder());
    }

    public function test_deve_persistir_transacao_bancaria_no_banco_de_dados(): void
    {
        // Arrange
        $transacaoBancaria = $this->transacaoBancariaDirector->buildTransacao();

        // Act
        $transacaoBancaria->save();
        $transacaoPersistida = TransacaoBancaria::find($transacaoBancaria->id);

        // Assert
        $this->assertNotEmpty($transacaoPersistida);
        $this->assertEquals($transacaoBancaria->id, $transacaoPersistida->id);
    }

    public function test_nao_deve_alterar_dados_transacao_ja_existente_no_banco_de_dados() : void
    {
        // Assert
        $this->expectException(\BadMethodCallException::class);

        // Arrange
        $transacaoBancaria = $this->transacaoBancariaDirector->buildTransacao();

        // Act
        $transacaoBancaria->save();

        $transacaoPersistida = TransacaoBancaria::find($transacaoBancaria->id);

        $transacaoPersistida->valor = 9999;
        $transacaoPersistida->save();
    }

    public function test_nao_deve_alterar_dados_transacao_ja_existente_no_banco_de_dados_update() : void
    {
        // Assert
        $this->expectException(\BadMethodCallException::class);

        // Arrange
        $transacaoBancaria = $this->transacaoBancariaDirector->buildTransacao();

        // Act
        $transacaoBancaria->save();

        $transacaoBancaria->update([
            'valor' => 9999
        ]);
    }
}
