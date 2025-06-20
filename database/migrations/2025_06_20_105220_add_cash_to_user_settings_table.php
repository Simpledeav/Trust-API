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
        Schema::table('user_settings', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'min_cash_deposit',
                'max_cash_deposit',
                'min_cash_withdrawal',
                'max_cash_withdrawal'
            ]);
            
            // Add new columns
            $table->decimal('min_cash_bank_deposit')->default(0);
            $table->decimal('min_cash_crypto_deposit')->default(0);
            $table->decimal('max_cash_bank_deposit')->default(0);
            $table->decimal('max_cash_crypto_deposit')->default(0);
            $table->decimal('min_cash_bank_withdrawal')->default(0);
            $table->decimal('min_cash_crypto_withdrawal')->default(0);
            $table->decimal('max_cash_bank_withdrawal')->default(0);
            $table->decimal('max_cash_crypto_withdrawal')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            // Reverse the changes if needed
            $table->dropColumn([
                'min_cash_bank_deposit',
                'min_cash_crypto_deposit',
                'max_cash_bank_deposit',
                'max_cash_crypto_deposit',
                'min_cash_bank_withdrawal',
                'min_cash_crypto_withdrawal',
                'max_cash_bank_withdrawal',
                'max_cash_crypto_withdrawal'
            ]);
            
            $table->decimal('min_cash_deposit')->default(0);
            $table->decimal('max_cash_deposit')->default(0);
            $table->decimal('min_cash_withdrawal')->default(0);
            $table->decimal('max_cash_withdrawal')->default(0);
        });
    }
};
