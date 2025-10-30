<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('produk', function (Blueprint $table) {
        $table->id('produkID');
        $table->string('namaProduk');
        $table->string('image')->nullable(); // path gambar
        $table->string('category');
        $table->integer('stock')->default(0);
        $table->decimal('harga', 10, 2); // contoh: 15000.00
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
