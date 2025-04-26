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
            $table->string('btc_wallet')->nullable();
            $table->string('eth_wallet')->nullable();
            $table->string('trc_wallet')->nullable();
            $table->string('erc_wallet')->nullable();
            $table->text('wallet_note')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->string('bank_reference')->nullable();
            $table->string('bank_address')->nullable();

            $table->decimal('min_cash_deposit')->default(10);
            $table->decimal('max_cash_deposit')->default(100000);
            $table->decimal('min_cash_withdrawal')->default(10);
            $table->decimal('max_cash_withdrawal')->default(100000);
            
            $table->timestamps();
        });

        \App\Models\Setting::create([
            'id' => Str::uuid(),
            'btc_wallet' => 'bc1q39kyu8mqefa9dnkktg3zgmy8pcsuwjfggl36tr',
            'eth_wallet' => '0x11Eb012F8BD2D6E809306D194B4C29eA6397f6a5',
            'trc_wallet' => 'TR3AsEKegxbWiAgufQx29cHFEmiCXZqCbC',
            'erc_wallet' => '0x11Eb012F8BD2D6E809306D194B4C29eA6397f6a5',
            'wallet_note' => 'Default note',
            'bank_name' => 'Barclays',
            'bank_number' => '123456',
            'bank_account_name' => 'Itrust Assets Management LLC',
            'bank_account_number' => '86167282',
            'bank_routing_number' => '20-30-18',
            'bank_reference' => 'RS56TUBD4',
            'bank_address' => '3320 Tates Creek Rd, Lexington, KY 40502',
            'min_cash_deposit' => '10',
            'max_cash_deposit' => '100000',
            'min_cash_withdrawal' => '10',
            'max_cash_withdrawal' => '100000',
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
