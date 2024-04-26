<?php

namespace Tests\Feature;

use App\Models\BankTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Builder\BankTransactionBuilder;
use Tests\Builder\BankTransactionDirector;
use Tests\TestCase;

class BankTransactionTest extends TestCase
{
    use RefreshDatabase;

    private BankTransactionDirector $BankTransactionDirector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->BankTransactionDirector = new BankTransactionDirector(new BankTransactionBuilder());
    }

    public function test_deve_persistir_transacao_bancaria_no_banco_de_dados(): void
    {
        // Arrange
        $bankTransaction = $this->BankTransactionDirector->build();

        // Act
        $bankTransaction->save();
        $persistedTransaction = BankTransaction::find($bankTransaction->id);

        // Assert
        $this->assertNotEmpty($persistedTransaction);
        $this->assertEquals($bankTransaction->id, $persistedTransaction->id);
    }

    public function test_nao_deve_alterar_dados_transacao_ja_existente_no_banco_de_dados() : void
    {
        // Assert
        $this->expectException(\BadMethodCallException::class);

        // Arrange
        $bankTransaction = $this->BankTransactionDirector->build();

        // Act
        $bankTransaction->save();

        $persistedTransaction = BankTransaction::find($bankTransaction->id);

        $persistedTransaction->value = 9999;
        $persistedTransaction->save();
    }

    public function test_nao_deve_alterar_dados_transacao_ja_existente_no_banco_de_dados_update() : void
    {
        // Assert
        $this->expectException(\BadMethodCallException::class);

        // Arrange
        $bankTransaction = $this->BankTransactionDirector->build();

        // Act
        $bankTransaction->save();

        $bankTransaction->update([
            'value' => 9999
        ]);
    }
}
