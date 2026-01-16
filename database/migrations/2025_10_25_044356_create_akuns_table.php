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
        Schema::create('akun', function (Blueprint $table) {
           $table->string('id_karyawan', 5)->primary();
            $table->string('username', 20);
            $table->text('password');
            $table->enum('status_login', ['1', '2']);
            $table->string('id_buat', 5)->nullable();
            $table->string('id_update', 5)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            // Relasi ke tabel karyawan
            $table->foreign('id_karyawan')
                ->references('id')
                ->on('karyawan')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // Relasi ke jabatan pembuat & pengubah
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
