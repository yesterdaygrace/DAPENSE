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
        Schema::create('jurnalings', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_jurnal');
            $table->string('nomor_bukti');
            $table->string('keterangan');
            $table->string('kategori_jurnal');
            $table->string('debit');
            $table->string('kredit');
            $table->foreignId('coa_id')->constrained('coas')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnalings');
    }
};
