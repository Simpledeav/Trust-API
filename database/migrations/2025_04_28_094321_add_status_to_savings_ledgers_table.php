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
        Schema::table('savings_ledgers', function (Blueprint $table) {
            $table->enum('status', ['approved', 'pending', 'in_progress', 'declined'])
                  ->default('approved')
                  ->after('method');
        });

        // Update all existing records to have status 'approved'
        DB::table('savings_ledgers')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_ledgers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
