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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 0)->nullable()->after('price');

            $table->decimal('discount_amount', 10, 0)->default(0)->after('unit_cost');

            $table->decimal('subtotal', 10, 0)->after('discount_amount');

            $table->string('product_name')->nullable()->after('product_id');
            $table->string('product_category')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_cost',
                'discount_amount',
                'subtotal',
                'product_name',
                'product_category'
            ]);
        });
    }
};