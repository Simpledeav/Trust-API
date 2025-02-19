<?php

use Illuminate\Support\Str;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wallet_name')->nullable();
            $table->string('wallet_address')->nullable();
            $table->text('wallet_note')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->string('bank_reference')->nullable();
            $table->string('bank_address')->nullable();
            $table->timestamps();
        });

        \App\Models\Setting::create([
            'id' => Str::uuid(),
            'wallet_name' => 'Default Wallet',
            'wallet_address' => '0x123456789ABCDEF',
            'wallet_note' => 'Default note',
            'bank_name' => 'Default Bank',
            'bank_number' => '123456',
            'bank_account_name' => 'Default Bank Name',
            'bank_account_number' => '0000000000',
            'bank_routing_number' => '999999',
            'bank_reference' => 'Default Reference',
            'bank_address' => 'Default Address',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
