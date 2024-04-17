<?php

use App\Http\Controllers\ContaController;
use App\Http\Controllers\TransacaoController;
use Illuminate\Support\Facades\Route;

Route::resource('conta', ContaController::class);

Route::post('/transacao', [TransacaoController::class, 'transacao'])->name('transacao');