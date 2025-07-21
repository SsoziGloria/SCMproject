<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value')->nullable();
            $table->foreignId('user_id')->nullable()->comment('For user-specific settings');
            $table->text('description')->nullable();
            $table->string('type')->default('text');
            $table->timestamps();
            
            $table->unique(['key', 'user_id']);
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            'key' => 'show_supplier_products',
            'value' => '1',
            'user_id' => null, // null means it's a global setting
            'description' => 'Show or hide products from all suppliers on the store',
            'type' => 'boolean',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};