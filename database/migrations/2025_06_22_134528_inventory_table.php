<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->string('product_name');
            $table->integer('quantity')->default(0);
            $table->string('unit')->default('pcs');
            $table->string('batch_number')->nullable();
            $table->string('status')->default('available');
            $table->date('received_date')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('location')->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};