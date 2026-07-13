<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_reads', function (Blueprint $table) {
            $table->id();
            $table->string('epc', 64)->unique();
            $table->unsignedTinyInteger('ant')->default(0);
            $table->float('rssi')->default(0);
            $table->unsignedInteger('count')->default(1);
            $table->string('first_time', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_reads');
    }
};
