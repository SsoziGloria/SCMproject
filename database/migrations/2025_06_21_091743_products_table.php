<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Laravel default ID
            $table->string('product_id')->unique(); // Custom product code or SKU
            $table->string('name');
            $table->text('ingredients')->nullable(); // Optional field
            $table->decimal('price', 10, 2); // Up to 99999999.99
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Path to product image
            $table->boolean('featured')->default(false); // For special listing
            $table->unsignedInteger('stock')->default(0); // Quantity available
            $table->string('category')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable(); // Foreign key if needed
            $table->timestamps(); // created_at and updated_at

            // Indexes and foreign keys

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};