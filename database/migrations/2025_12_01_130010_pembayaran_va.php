<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_va', function (Blueprint $table) {
            $table->string('id', 25)->primary();
            $table->bigInteger('nominal')->unsigned();
            $table->string('id_order', 25);
            $table->string('channel', 100)->nullable();
            $table->dateTime('tanggal_transaksi')->nullable();
            $table->string('payment_link', 100)->nullable();
            $table->string('nomor_ref', 100)->nullable();
            $table->string('id_buat', 25)->nullable();
            $table->string('id_update', 25)->nullable();
            $table->dateTime('tanggal_buat')->nullable();
            $table->dateTime('tanggal_update')->nullable();

            $table->foreign('id_order')
                ->references('id')
                ->on('order')
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
        Schema::dropIfExists('pembayaran_va');
    }
};
