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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('id', 25)->primary();
            $table->string('id_jabatan', 5);
            $table->string('nama', 100);
            $table->text('alamat')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('no_hp', 13)->nullable();
            $table->enum('status', ['Y', 'N']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('id_buat', 5)->nullable();
            $table->string('id_update', 5)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            // Relasi ke jabatan utama
            $table->foreign('id_jabatan')
                ->references('id')
                ->on('jabatan')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            // Relasi ke jabatan pembuat & pengubah data
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
        Schema::dropIfExists('karyawan');
    }
};
