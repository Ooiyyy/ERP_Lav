<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('nama', 100);
            $table->integer('stok_bahan_baku')->default(0);
            $table->string('satuan', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('id_buat')->nullable();
            $table->string('id_update')->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

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
        Schema::dropIfExists('bahan_baku');
    }
};