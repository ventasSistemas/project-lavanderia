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
        Schema::create('complementary_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complementary_product_category_id')
                  ->constrained('complementary_product_categories')
                  ->onDelete('cascade');

            $table->foreignId('branch_id')->nullable()
                  ->constrained('branches')
                  ->onDelete('cascade');

            $table->string('name', 120);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->enum('state', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complementary_products');
    }
};
