<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->primary();
            $table->integer('customer_id')->nullable(false);
            $table->float('quantity')->nullable();
            $table->float('total_quantity')->nullable();
            $table->integer('purchase_count')->nullable();
            $table->integer('cluster')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index('customer_id');
            $table->index('cluster');
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
