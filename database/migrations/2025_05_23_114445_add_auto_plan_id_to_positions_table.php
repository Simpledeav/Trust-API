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
        Schema::table('positions', function (Blueprint $table) {
            $table->uuid('auto_plan_investment_id')->nullable()->after('account');
            $table->decimal('dividends', 18, 2)->default(0)->after('auto_plan_investment_id');
            $table->foreign('auto_plan_investment_id')
                ->references('id')
                ->on('auto_plan_investments')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn([
                'auto_plan_investment_id',
                'dividends'
            ]);
        });
    }
};
