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
        Schema::create('coas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun');
            $table->string('nama_akun');
            $table->string('saldo_normal');
            $table->string('kategori');
            $table->integer('level');
            $table->foreignId('header_coa_id')->constrained('header_coas')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['kode_akun', 'nama_akun']);
        });    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coas');
    }
};
