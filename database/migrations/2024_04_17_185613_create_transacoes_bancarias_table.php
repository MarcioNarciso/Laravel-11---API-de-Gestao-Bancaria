<?php

use App\Enums\FormaPagamento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transacoes_bancarias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recebedor_id');
            $table->bigInteger('pagador_id');
            $table->enum('forma_pagamento', [
                FormaPagamento::CREDITO->value, FormaPagamento::DEBITO->value,
                FormaPagamento::PIX->value
            ]);
            $table->float('valor');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_bancarias');
    }
};
