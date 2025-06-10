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
        Schema::create('auto_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->decimal('min_invest', 18, 2)->default(10);
            $table->decimal('max_invest', 18, 2)->default(10000);
            $table->decimal('win_rate', 18, 2)->default(10000);
            $table->string('duration');
            $table->string('milestone');
            $table->string('aum');
            $table->string('expected_returns');
            $table->string('day_returns');
            $table->string('type');
            $table->enum('status', ['active', 'locked'])->default('active');
            $table->text('img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_plans');
    }
};
