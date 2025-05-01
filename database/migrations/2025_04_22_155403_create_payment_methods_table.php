<?php

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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('type', ['bank', 'crypto']);
            $table->string('label')->nullable(); // e.g. "Main Bank", "USDT TRC20"
            $table->string('currency')->nullable(); // e.g. NGN, USD, USDT
            $table->string('wallet_address')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('bank_reference')->nullable();
            $table->string('bank_address')->nullable();
            $table->boolean('is_withdrawal')->default(true); // true if for withdrawal
            $table->timestamps();


            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
