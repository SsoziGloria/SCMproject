<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('status')->default('pending'); // e.g. pending, shipped, delivered, cancelled
            $table->date('expected_delivery')->nullable();
            $table->date('shipped_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};