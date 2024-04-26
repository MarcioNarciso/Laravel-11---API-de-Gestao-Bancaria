<?php

namespace App\Helpers;

/**
 * Formata o dinheiro no formato americano, mas sem o separador de milhar.
 * 
 * @param   float       $value
 * @return  bool|string
 */
if (! function_exists('formatCurrency')) {
    function formatCurrency(float $value) : bool|string
    {
        return number_format($value, 2, '.', '');
    }
}