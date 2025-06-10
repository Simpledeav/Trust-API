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
        Schema::table('user_settings', function (Blueprint $table) {
            $table->enum('trade', ['regular', 'option'])->default('regular');
            $table->string('beneficiary_first_name')->nullable();
            $table->string('beneficiary_last_name')->nullable();
            $table->string('beneficiary_nationality')->nullable();
            $table->string('beneficiary_dob')->nullable();
            $table->string('beneficiary_email')->nullable();
            $table->string('beneficiary_phone')->nullable();
            $table->string('beneficiary_address')->nullable();
            $table->string('beneficiary_country')->nullable();
            $table->string('beneficiary_state')->nullable();
            $table->string('beneficiary_city')->nullable();
            $table->string('beneficiary_zipcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            //
        });
    }
};
