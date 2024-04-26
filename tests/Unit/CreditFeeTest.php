<?php

namespace Tests\Unit;

use App\Models\FeeCalculationRules\CreditFee;
use PHPUnit\Framework\TestCase;

class CreditFeeTest extends TestCase
{
    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_credito(): void
    {
        $transactionValue = 100;

        $transactionFeeValue = (new CreditFee())->calculate($transactionValue);

        $this->assertEquals(($transactionValue * (5 / 100)), $transactionFeeValue);
    }

    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_credito_negativo() : void
    {
        $transactionValue = -100;

        $transactionFeeValue = (new CreditFee())->calculate($transactionValue);

        $this->assertEquals(( 100 * 0.05), $transactionFeeValue);
    }
}
