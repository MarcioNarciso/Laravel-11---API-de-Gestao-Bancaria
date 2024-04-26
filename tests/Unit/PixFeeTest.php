<?php

namespace Tests\Unit;

use App\Models\FeeCalculationRules\PixFee;
use PHPUnit\Framework\TestCase;

class PixFeeTest extends TestCase
{
    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_pix(): void
    {
        $transactionValue = 100;

        $transactionFeeValue = (new PixFee())->calculate($transactionValue);

        $this->assertEquals(($transactionValue * 0.0), $transactionFeeValue);
    }

    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_pix_negativo() : void
    {
        $transactionValue = -100;

        $transactionFeeValue = (new PixFee())->calculate($transactionValue);

        $this->assertEquals(0, $transactionFeeValue);
    }
}
