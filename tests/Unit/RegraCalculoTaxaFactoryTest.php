<?php

namespace Tests\Unit;

use App\Enums\FormaPagamento;
use App\Factories\RegraCalculoTaxaFactory;
use App\Models\RegrasCalculoTaxas\TaxaDoPix;
use PHPUnit\Framework\TestCase;

class RegraCalculoTaxaFactoryTest extends TestCase
{
    public function test_deve_instanciar_regra_de_calculo_de_taxa_existente(): void
    {
        $regraDeCalculo = RegraCalculoTaxaFactory::make(FormaPagamento::PIX);

        $this->assertInstanceOf(TaxaDoPix::class, $regraDeCalculo);
    }
}
