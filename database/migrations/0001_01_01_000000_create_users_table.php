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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('avatar')->nullable();
            $table->uuid('country_id')->constrained()->references('id')->on('countries');
            $table->uuid('state_id')->constrained()->references('id')->on('states');
            $table->uuid('city_id')->constrained()->references('id')->on('cities');
            $table->string('zipcode');
            $table->string('ssn');
            $table->dateTime('dob')->nullable();
            $table->string('nationality');
            $table->string('experience');
            $table->string('employed');
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->enum('kyc', ['approved', 'pending', 'declined'])->default('pending');
            $table->uuid('currency_id')->constrained()->references('id')->on('currencies');
            $table->string('id_number')->nullable();
            $table->text('front_id')->nullable();
            $table->text('back_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('two_fa_activated_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
