<?php

namespace Tests\Unit;

use App\Models\FeeCalculationRules\DebitFee;
use PHPUnit\Framework\TestCase;

class DebitFeeTest extends TestCase
{
    
    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_debito(): void
    {
        $transactionValue = 100;

        $transactionFeeValue = (new DebitFee())->calculate($transactionValue);

        $this->assertEquals(($transactionValue * (3 / 100)), $transactionFeeValue);
    }

    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_debito_negativo() : void
    {
        $transactionValue = -100;

        $transactionFeeValue = (new DebitFee())->calculate($transactionValue);

        $this->assertEquals((100 * (3 / 100)), $transactionFeeValue);
    }
}
