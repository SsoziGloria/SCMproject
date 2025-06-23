<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Foreign key for products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->nullOnDelete(); // or ->cascadeOnDelete() if preferred
        });

        // Foreign key for inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });
    }
};