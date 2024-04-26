<?php

namespace Tests\Unit;

use App\Enums\PaymentMethod;
use App\Factories\FeeCalculationRuleFactory;
use App\Models\FeeCalculationRules\PixFee;
use PHPUnit\Framework\TestCase;

class FeeCalculationRuleFactoryTest extends TestCase
{
    public function test_deve_instanciar_regra_de_calculo_de_taxa_existente(): void
    {
        $feeCalculationRule = (new FeeCalculationRuleFactory())->make(PaymentMethod::PIX);

        $this->assertInstanceOf(PixFee::class, $feeCalculationRule);
    }
}
