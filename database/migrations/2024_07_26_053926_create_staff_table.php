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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('plain_password');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default('0');
            $table->string('otp')->nullable();
            $table->string('otp_verified')->nullable();
            $table->timestamp('otp_created_at')->nullable();
            $table->longText('google_id')->nullable();
            $table->longText('facebook_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
