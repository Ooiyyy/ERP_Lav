<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->string('id', 25)->primary();         // varchar(25) PK
            $table->string('id_sales', 25)->nullable(); // fk ke karyawan (jika ada)
            $table->string('id_toko', 25)->nullable();  // fk ke toko (jika ada)
            $table->enum('jenis_bayar', ['cash', 'transfer', 'midtrans'])->nullable();
            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            $table->foreign('id_sales')
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

    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
