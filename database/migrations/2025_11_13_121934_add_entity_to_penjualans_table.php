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
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('kodePesanan')->unique()->nullable();
            $table->string('status')->default('pending');
            $table->decimal('diskon', 10, 2)->default('0')->nullable();
            $table->decimal('uangBayar', 10, 2)->nullable();
            $table->decimal('kembalian', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            //
        });
    }
};
