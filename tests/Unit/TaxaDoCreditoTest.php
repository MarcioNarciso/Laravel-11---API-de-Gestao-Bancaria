<?php

namespace Tests\Unit;

use App\Models\RegrasCalculoTaxas\CreditFee;
use PHPUnit\Framework\TestCase;

class TaxaDoCreditoTest extends TestCase
{
    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_credito(): void
    {
        $valorDaTransacao = 100;

        $valorDaTaxaDaTransacao = (new CreditFee())->calculate($valorDaTransacao);

        $this->assertEquals(($valorDaTransacao * (5 / 100)), $valorDaTaxaDaTransacao);
    }

    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_credito_negativo() : void
    {
        $valorDaTransacao = -100;

        $valorDaTaxaDaTransacao = (new CreditFee())->calculate($valorDaTransacao);

        $this->assertEquals(( 100 * 0.05), $valorDaTaxaDaTransacao);
    }
}
