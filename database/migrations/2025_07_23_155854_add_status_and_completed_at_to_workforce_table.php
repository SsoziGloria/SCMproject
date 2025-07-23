<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workforces', function (Blueprint $table) {
            $table->string('status')->default('assigned')->after('task');
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('workforce', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('completed_at');
        });
    }
};
