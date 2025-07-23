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
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('customer_id');
            $table->float('quantity')->nullable();
            $table->float('total_quantity')->nullable();
            $table->integer('purchase_count')->nullable();
            $table->integer('cluster')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segments');
    }
};
