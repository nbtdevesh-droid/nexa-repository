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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->string('coupon_type');
            $table->string('product_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('amount_type')->nullable();
            $table->string('amount')->nullable();
            $table->string('product_min_amount')->nullable();
            $table->string('max_uses')->nullable();
            $table->string('remain_uses')->nullable();
            $table->integer('main_category')->nullable();
            $table->integer('sub_category')->nullable();
            $table->boolean('status')->default('1');
            $table->date('coupon_start_date');
            $table->date('coupon_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
