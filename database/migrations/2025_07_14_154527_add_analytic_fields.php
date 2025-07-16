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
        Schema::table('orders', function (Blueprint $table) {
            // Sales channel tracking
            $table->string('sales_channel')->nullable()->after('user_id');

            $table->decimal('subtotal', 10, 0)->nullable()->after('total_amount');
            $table->decimal('shipping_fee', 10, 0)->nullable()->after('subtotal');
            $table->decimal('discount_amount', 10, 0)->default(0.00)->after('shipping_fee');

            $table->string('referral_source')->nullable()->after('notes');

            $table->string('shipping_region')->nullable()->after('shipping_city');

            $table->string('tracking_number')->nullable()->after('shipping_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'sales_channel', //
                'subtotal',
                'shipping_fee',
                'discount_amount',
                'referral_source',
                'shipping_region',
                'tracking_number'
            ]);
        });
    }
};