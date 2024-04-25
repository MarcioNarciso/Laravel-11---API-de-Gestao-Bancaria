<?php

namespace App\Helpers;

if (! function_exists('formatCurrency')) {
    function formatCurrency($value) : bool|string
    {
        return number_format($value, 2, '.', '');
    }
}