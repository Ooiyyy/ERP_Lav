<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->string('id', 25)->primary();
            $table->string('nama', 100);
            $table->string('no_hp', 13);
            $table->text('alamat')->nullable(); // alamat tidak wajib diisi

            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
