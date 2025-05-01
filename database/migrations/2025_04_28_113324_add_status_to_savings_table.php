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
        Schema::table('savings', function (Blueprint $table) {
            $table->enum('status', ['active', 'locked'])->default('active')->after('old_balance');
            $table->enum('trading', ['active', 'locked'])->default('active')->after('status');
            $table->string('locked_account_message')->default('Account actions locked, Contact support for more details')->after('trading');
            $table->string('locked_trading_message')->default('Trade actions locked, Contact support for more details')->after('locked_account_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'trading',
                'locked_account_message',
                'locked_trading_message',
            ]);
        });
    }
};
