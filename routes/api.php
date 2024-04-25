<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransacaoController;
use Illuminate\Support\Facades\Route;

/**
 * "apiResource" ignora automaticamente os métodos "create" e "edit", que normalmente
 * têm como retorno código HTML.
 * 
 * "only" define quais ações a controller deve tratar.
 */
Route::apiResource('contas', AccountController::class)->only([
    'index', 'show', 'store', 'destroy'
]);

Route::post('/transacao', [TransacaoController::class, 'transacao'])->name('transacao');
Route::get('/transacao/{contaId}', [TransacaoController::class, 'listarTransacoes'])->name('listarTransacoes');