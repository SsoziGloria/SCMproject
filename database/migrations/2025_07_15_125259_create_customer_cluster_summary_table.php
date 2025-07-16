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
        Schema::create('customer_cluster_summary', function (Blueprint $table) {
            $table->id();
        $table->integer('cluster')->index();
        $table->text('description')->nullable();
        $table->float('customer_count');
        $table->text('product_types')->nullable();
        $table->text('recommendation_strategy')->nullable();
        $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_cluster_summary');
    }
};
