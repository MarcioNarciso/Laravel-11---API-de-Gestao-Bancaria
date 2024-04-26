<?php 

namespace App\Factories;
use App\Enums\PaymentMethod;
use App\Interfaces\Factories\FeeCalculationRuleFactoryInterface;
use App\Models\FeeCalculationRules\{
    CreditFee, 
    DebitFee, 
    PixFee,
    FeeCalculationRule
};
use App\Exceptions\NonExistFeeCalculcationRuleException;

/**
 * Classe que lida com a lógica de criação da instância da regra de cálculo da
 * taxa sobre a forma de pagamento.
 * 
 * Cria uma dependência entre a forma de pagamento e sua taxa, de forma que não
 * se pode utilizar uma forma de pagamento sem definir uma taxa para ela.
 */
class FeeCalculationRuleFactory implements FeeCalculationRuleFactoryInterface
{
    private static array $rules = [
        PaymentMethod::CREDIT->value => CreditFee::class,
        PaymentMethod::DEBIT->value => DebitFee::class,
        PaymentMethod::PIX->value => PixFee::class
    ];

    /**
     * Instancia a classe da regra de cálculo da taxa com base na forma de pagamento.
     * 
     * Se a forma de pagamento não tiver uma regra definida, é lançada uma exceção.
     * 
     * @param PaymentMethod $paymentMethod
     * @return \App\Models\FeeCalculationRules\FeeCalculationRule
     * 
     * @throws \App\Exceptions\NonExistFeeCalculcationRuleException
     */
    public function make(PaymentMethod $paymentMethod) : FeeCalculationRule
    {
        /**
         * Obtém a classe que deve ser instanciada com base na forma de pagamento.
         */
        $calculationRule = self::$rules[$paymentMethod->value];

        /**
         * Se não existir a classe da regra de cálculo correspondente à forma de 
         * pagamento, é lançada uma exceção.
         */
        if (empty($calculationRule)) {
            throw new NonExistFeeCalculcationRuleException('Forma de Pagamento inválida.');
        }

        return new $calculationRule();
    }
}
