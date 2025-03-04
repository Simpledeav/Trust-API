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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->decimal('amount', 18, 2)->default(0);
            $table->enum('type', ['debit', 'credit']);
            $table->enum('account', ['wallet', 'brokerage', 'auto']);
            $table->decimal('balance', 18, 2)->default(0);
            $table->decimal('old_balance', 18, 2)->default(0);
            $table->uuidMorphs('ledgerable');
            $table->string('comment')->nullable();
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
