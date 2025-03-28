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
        Schema::create('savings_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('savings_id');
            $table->decimal('amount', 18, 2)->default(0);
            $table->enum('type', ['debit', 'credit']);
            $table->enum('method', ['contribution', 'interest']);
            $table->decimal('balance', 18, 2)->default(0);
            $table->decimal('old_balance', 18, 2)->default(0);
            $table->string('comment')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('savings_id')
                ->references('id')
                ->on('savings')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_ledgers');
    }
};
