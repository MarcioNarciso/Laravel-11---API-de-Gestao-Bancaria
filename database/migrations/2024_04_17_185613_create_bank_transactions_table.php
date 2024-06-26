<?php

use App\Enums\PaymentMethod;
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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('receiverId');
            $table->bigInteger('payerId');
            $table->enum('paymentMethod', [
                PaymentMethod::CREDIT->value, PaymentMethod::DEBIT->value,
                PaymentMethod::PIX->value
            ]);
            $table->float('value');
            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
