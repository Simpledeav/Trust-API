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
        Schema::table('savings_accounts', function (Blueprint $table) {
            $table->decimal('min_contribution', 18, 2)->default(10)->after('rate');
            $table->decimal('max_contribution', 18, 2)->default(10000)->after('min_contribution');
            $table->decimal('min_cashout', 18, 2)->default(10)->after('max_contribution');
            $table->decimal('max_cashout', 18, 2)->default(10000)->after('min_cashout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'min_contribution',
                'max_contribution',
                'min_cashout',
                'max_cashout'
            ]);
        });
    }
};
