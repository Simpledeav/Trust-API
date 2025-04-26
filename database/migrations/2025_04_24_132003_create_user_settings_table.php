<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->decimal('min_cash_deposit')->default(0);
            $table->decimal('max_cash_deposit')->default(0);
            $table->decimal('min_cash_withdrawal')->default(0);
            $table->decimal('max_cash_withdrawal')->default(0);
            $table->boolean('locked_cash')->default(false);
            $table->string('locked_cash_message')->default('Unauthorized, Contact support for more details');
            $table->boolean('locked_bank_deposit')->default(true);
            $table->string('locked_bank_deposit_message')->default('Unauthorized, Contact support for more details');
            $table->string('connect_wallet_network')->nullable();
            $table->string('connect_wallet_phrase')->nullable();
            $table->boolean('drip')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        \App\Models\User::chunk(100, function ($users) {
            foreach ($users as $user) {
                \App\Models\UserSettings::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        // other defaults...
                    ]
                );
            }
        });

        // Update Transactions status table 
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('approved', 'declined', 'pending', 'in_progress', 'cancelled') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
