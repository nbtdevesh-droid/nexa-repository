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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('slug');
            $table->longText('description');
            $table->unsignedBigInteger('parent_category');
            $table->unsignedBigInteger('child_category');
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->foreign('parent_category')
                ->references('id')
                ->on('categories') // Ensure 'categories' is the correct table name
                ->onDelete('cascade');

            $table->foreign('child_category')
                ->references('id')
                ->on('categories') // Ensure 'categories' is the correct table name
                ->onDelete('cascade');

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->integer('quantity')->nullable();
            $table->string('regular_price')->nullable();
            $table->string('sale_price')->nullable();
            $table->string('sku')->nullable();
            $table->string('feature_image');
            $table->longText('gallery_image')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->boolean('status')->default('1');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
