<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_order', function (Blueprint $table) {
            $table->string('id', 25)->primary();
            $table->string('id_transaksi', 25); // fk -> order.id
            $table->string('id_produk', 25);    // fk -> produk.id
            $table->integer('jumlah')->default(1);
            $table->integer('harga')->default(0);
            $table->string('satuan', 50)->nullable();
            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            $table->foreign('id_transaksi')
                ->references('id')
                ->on('order')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_produk')
                ->references('id')
                ->on('produk')
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

            // index untuk performa pencarian
            $table->index('id_transaksi');
            $table->index('id_produk');


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_order');
    }
};
