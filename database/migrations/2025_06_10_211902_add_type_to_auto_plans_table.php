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
        Schema::table('auto_plans', function (Blueprint $table) {
            $table->string('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::enableForeignKeyConstraints();
        Schema::table('auto_plans', function (Blueprint $table) {
            $table->dropColumn([
                'type'
            ]);
        });
        Schema::disableForeignKeyConstraints();
    }
};
