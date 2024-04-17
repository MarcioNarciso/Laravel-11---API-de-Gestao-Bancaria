<?php

namespace App\Helpers;

if (! function_exists('formatar_dinheiro')) {
    function formatCurrency($valor) : bool|string
    {
        return number_format($valor, 2, '.', '');
    }
}