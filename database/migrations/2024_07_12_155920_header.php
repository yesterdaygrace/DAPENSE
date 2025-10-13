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
        Schema::create('header_coas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_header');
            $table->string('nama_header');
            $table->integer('level');
            $table->foreignId('parent_id')->nullable()->constrained('header_coas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_coas');
    }
};
