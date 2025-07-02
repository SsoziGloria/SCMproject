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
            $table->date('manufactured_date')->nullable();
            $table->string('status')->default('available');
            $table->date('received_date')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('location')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};