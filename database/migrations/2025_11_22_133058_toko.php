<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toko', function (Blueprint $table) {
            $table->string('id', 25)->primary();

            $table->string('id_karyawan', 25);
            $table->string('id_pelanggan', 25);

            $table->string('nama_toko', 50);
            $table->text('alamat');
            $table->string('lintang', 50)->nullable();
            $table->string('bujur', 50)->nullable();

            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            // Foreign key (opsional, sesuaikan jika tabel & tipe datanya berbeda)
            $table->foreign('id_karyawan')
                ->references('id')
                ->on('karyawan')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('id_pelanggan')
                ->references('id')
                ->on('pelanggan')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::table('toko', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
            $table->dropForeign(['id_pelanggan']);
        });

        Schema::dropIfExists('toko');
    }
};
