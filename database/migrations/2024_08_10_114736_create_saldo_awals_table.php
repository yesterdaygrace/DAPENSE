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
        Schema::create('saldo_awal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('coas')->onDelete('cascade');
            $table->date('tanggal_saldo');
            $table->decimal('debit', 15, 2);
            $table->decimal('kredit', 15, 2);
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_awal');
    }
};
