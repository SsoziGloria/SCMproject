<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Laravel default ID
            $table->string('product_id')->unique(); // Custom product code
            $table->string('name');
            $table->text('ingredients')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Path to product image
            $table->boolean('featured')->default(false); // For special listing
            $table->unsignedInteger('stock')->default(0);
            $table->string('category')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable(); // Foreign key if needed
            $table->timestamps(); // created_at and updated_at
        });
        // Indexes and foreign keys
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('set null');

            $table->foreign('category')
                ->references('name')
                ->on('categories')
                ->onDelete('set null');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};