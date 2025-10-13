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
        Schema::create('neraca_saldos', function (Blueprint $table) {
            $table->id();
            $table->string('coa_id'); // Sesuaikan dengan tipe data kode_akun
            $table->foreign('coa_id')->references('kode_akun')->on('coas')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->date('month');
            $table->decimal('saldo_awal', 15, 2);
            $table->decimal('debit', 15, 2);
            $table->decimal('kredit', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neraca_saldos');
    }
};
