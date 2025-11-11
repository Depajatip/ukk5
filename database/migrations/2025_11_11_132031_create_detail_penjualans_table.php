<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detailPenjualans', function (Blueprint $table) {

            $table->id('detailID');
            $table->foreignId('penjualanID')
                ->constrained('penjualans', 'penjualanID')
                ->onDelete('cascade');
            $table->foreignId('produkID')
                ->constrained('products', 'produkID')
                ->onDelete('restrict');
            $table->integer('jumlahProduk')->unsigned();
            $table->decimal('subTotal', 12, 2);
            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailPenjualans');
    }
};
