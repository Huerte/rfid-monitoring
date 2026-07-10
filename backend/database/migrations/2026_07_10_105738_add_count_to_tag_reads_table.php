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
        Schema::table('tag_reads', function (Blueprint $table) {
            $table->unsignedInteger('count')->default(1)->after('rssi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tag_reads', function (Blueprint $table) {
            $table->dropColumn('count');
        });
    }
};
