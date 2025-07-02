<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id'); // Customer placing the order
            $table->unsignedBigInteger('supplier_id')->nullable(); // Optional: supplier fulfilling the order
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // e.g. pending, processing, shipped, delivered, cancelled
            $table->text('shipping_address')->nullable();
            $table->integer('quantity');
            $table->date('ordered_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->timestamp('order_date')->useCurrent();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
