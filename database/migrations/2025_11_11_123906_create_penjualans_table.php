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
Schema::create('penjualans', function (Blueprint $table) {
    $table->id('penjualanID');
    $table->foreignId('pelangganID')
          ->constrained('pelanggans', 'pelangganID')
          ->onDelete('restrict');
    $table->decimal('totalHarga', 12, 2);
    $table->dateTime('tanggalPenjualan')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
