<?php

namespace Tests\Unit;

use App\Models\RegrasCalculoTaxas\PixFee;
use PHPUnit\Framework\TestCase;

class TaxaDoPixTest extends TestCase
{
    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_pix(): void
    {
        $valorDaTransacao = 100;

        $valorDaTaxaDaTransacao = (new PixFee())->calculate($valorDaTransacao);

        $this->assertEquals(($valorDaTransacao * 0.0), $valorDaTaxaDaTransacao);
    }

    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_pix_negativo() : void
    {
        $valorDaTransacao = -100;

        $valorDaTaxaDaTransacao = (new PixFee())->calculate($valorDaTransacao);

        $this->assertEquals(0, $valorDaTaxaDaTransacao);
    }
}
