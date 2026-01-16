<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_bahan_baku_keluar', function (Blueprint $table) {
            $table->string('id', 50)->primary();

            $table->string('id_bahan_baku_keluar', 50); // FK manual
            $table->string('id_bahan_baku', 50); // FK manual

            $table->integer('jumlah')->default(0);
            $table->string('satuan', 50)->nullable();

            $table->string('id_buat', 50)->nullable();
            $table->string('id_update', 50)->nullable();
            $table->datetime('tanggal_buat')->nullable();
            $table->datetime('tanggal_update')->nullable();

            $table->foreign('id_bahan_baku_keluar')
                ->references('id')
                ->on('detail_bahan_baku_keluar')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_bahan_baku')
                ->references('id')
                ->on('bahan_baku')
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
        Schema::dropIfExists('detail_transaksi_bahan_baku_keluar');
    }
};
