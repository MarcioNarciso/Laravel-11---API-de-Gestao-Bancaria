<?php 

namespace App\Factories;
use App\Enums\FormaPagamento;
use App\Models\RegrasCalculoTaxas\{
    TaxaDoCredito, 
    TaxaDoDebito, 
    TaxaDoPix,
    RegraCalculoTaxa
};
use App\Exceptions\RegraCalculoTaxaInexistenteException;

class RegraCalculoTaxaFactory
{
    private static array $regras = [
        FormaPagamento::CREDITO->value => TaxaDoCredito::class,
        FormaPagamento::DEBITO->value => TaxaDoDebito::class,
        FormaPagamento::PIX->value => TaxaDoPix::class
    ];

    /**
     * Instancia a classe da regra de cálculo da taxa com base na forma de pagamento.
     * @param FormaPagamento $formaPagamento
     * @return \App\Models\RegrasCalculoTaxas\RegraCalculoTaxa
     * @throws \App\Exceptions\RegraCalculoTaxaInexistenteException
     */
    public function make(FormaPagamento $formaPagamento) : RegraCalculoTaxa
    {
        /**
         * Obtém a classe que deve ser instanciada com base na forma de pagamento.
         */
        $regraCalculo = self::$regras[$formaPagamento->value];

        /**
         * Se não existir a classe da regra de cálculo correspondente a forma de 
         * pagamento, é lançada uma exceção.
         */
        if (empty($regraCalculo)) {
            throw new RegraCalculoTaxaInexistenteException('Forma de Pagamento inválida.');
        }

        return new $regraCalculo();
    }
}
