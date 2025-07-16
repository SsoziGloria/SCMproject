<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

<<<<<<< HEAD
return new class extends Migration
{
=======
return new class extends Migration {
>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
<<<<<<< HEAD
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->integer('quantity');
        $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
        $table->timestamp('order_date')->useCurrent();
        $table->timestamps();
    });
=======
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->timestamp('order_date')->useCurrent();
            $table->timestamps();
        });
>>>>>>> d2dab711646aed7182ab7947b22aab29e487a426
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
