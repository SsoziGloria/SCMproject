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
        Schema::table('shipments', function (Blueprint $table) {
            // Add order relationship
            $table->unsignedBigInteger('order_id')->nullable()->after('id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Simplify status tracking - just these 4 states
            $table->dropColumn('status');
        });

        // Re-add status with simplified enum
        Schema::table('shipments', function (Blueprint $table) {
            $table->enum('status', ['processing', 'shipped', 'in_transit', 'delivered', 'cancelled'])->default('processing')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
            $table->dropColumn('status');
        });

        // Restore original status
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('quantity');
        });
    }
};
