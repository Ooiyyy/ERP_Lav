<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_produk_masuk', function (Blueprint $table) {
            $table->string('id', 25)->primary();

            $table->string('id_produk_masuk', 25);
            $table->string('id_bahan_baku', 25);

            $table->integer('jumlah');
            $table->integer('nominal');
            $table->string('satuan', 50);

            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            // Foreign Key
            $table->foreign('id_produk_masuk')->references('id')->on('produk_masuk');
            $table->foreign('id_bahan_baku')->references('id')->on('bahan_baku');
            $table->foreign('id_buat')->references('id_karyawan')->on('akun');
            $table->foreign('id_update')->references('id_karyawan')->on('akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_produk_masuk');
    }
};
