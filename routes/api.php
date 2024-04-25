<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/**
 * "apiResource" ignora automaticamente os métodos "create" e "edit", que normalmente
 * têm como retorno código HTML.
 * 
 * "only" define quais ações a controller deve tratar.
 */
Route::apiResource('accounts', AccountController::class)->only([
    'index', 'show', 'store', 'destroy'
]);

Route::post('/transactions', [TransactionController::class, 'transaction']);
Route::get('/transactions/{account}', [TransactionController::class, 'listTransactions']);