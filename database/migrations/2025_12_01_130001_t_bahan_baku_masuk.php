<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bahan_baku_masuk', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('id_karyawan_gudang', 50); // FK manual
            $table->datetime('tanggal_transaksi');
            $table->string('id_toko', 100)->nullable();
            $table->string('no_nota', 50)->nullable();

            $table->string('id_buat', 50)->nullable();
            $table->string('id_update', 50)->nullable();
            $table->datetime('tanggal_buat')->nullable();
            $table->datetime('tanggal_update')->nullable();

            $table->foreign('id_karyawan_gudang')
                ->references('id')
                ->on('karyawan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_toko')
                ->references('id')
                ->on('toko')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_buat')
                ->references('id_karyawan')
                ->on('akun')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_update')
                ->references('id_karyawan')
                ->on('akun')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bahan_baku_masuk');
    }
};
