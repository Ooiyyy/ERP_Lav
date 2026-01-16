<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_masuk', function (Blueprint $table) {
            $table->string('id', 25)->primary();

            $table->string('id_karyawan_gudang', 25);
            $table->string('id_karyawan_produksi', 25);

            $table->dateTime('tanggal_transaksi');

            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            // Foreign Key
            $table->foreign('id_karyawan_gudang')->references('id')->on('karyawan');
            $table->foreign('id_karyawan_produksi')->references('id')->on('karyawan');
            $table->foreign('id_buat')->references('id_karyawan')->on('akun');
            $table->foreign('id_update')->references('id_karyawan')->on('akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_masuk');
    }
};
