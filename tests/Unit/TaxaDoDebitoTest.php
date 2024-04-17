<?php

namespace Tests\Unit;

use App\Models\RegrasCalculoTaxas\TaxaDoDebito;
use PHPUnit\Framework\TestCase;

class TaxaDoDebitoTest extends TestCase
{
    
    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_debito(): void
    {
        $valorDaTransacao = 100;

        $valorDaTaxaDaTransacao = (new TaxaDoDebito())->calcularValorTaxa($valorDaTransacao);

        $this->assertEquals(($valorDaTransacao * 0.3), $valorDaTaxaDaTransacao);
    }

    public function test_deve_calcular_o_valor_da_transacao_com_base_na_taxa_do_debito_negativo() : void
    {
        $valorDaTransacao = -100;

        $valorDaTaxaDaTransacao = (new TaxaDoDebito())->calcularValorTaxa($valorDaTransacao);

        $this->assertEquals((100 * 0.3), $valorDaTaxaDaTransacao);
    }
}
