<?php

namespace Tests\Unit;

use App\Enums\PaymentMethod;
use App\Factories\FeeCalculationRuleFactory;
use App\Models\RegrasCalculoTaxas\PixFee;
use PHPUnit\Framework\TestCase;

class RegraCalculoTaxaFactoryTest extends TestCase
{
    public function test_deve_instanciar_regra_de_calculo_de_taxa_existente(): void
    {
        $regraDeCalculo = (new FeeCalculationRuleFactory())->make(PaymentMethod::PIX);

        $this->assertInstanceOf(PixFee::class, $regraDeCalculo);
    }
}
