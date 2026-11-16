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
        if (!Schema::hasTable('transaksi_simulasi_uts')) {
            Schema::create('transaksi_simulasi_uts', function (Blueprint $table) {
                $table->increments('id_simulasi');
                $table->string('order_id', 50)->unique();
                $table->integer('id_sppt');
                $table->string('nop', 25);
                $table->integer('tahun_pajak');
                $table->string('nama_wp', 150);
                $table->string('nama_pembayar', 150);
                $table->decimal('nominal_pembayaran', 15, 2);
                $table->string('metode_pembayaran', 50)->default('Midtrans Sandbox');
                $table->enum('status_pembayaran', ['Pending', 'Berhasil', 'Gagal'])->default('Pending');
                $table->string('snap_token', 255)->nullable();
                $table->string('transaction_id_gateway', 100)->nullable();
                $table->string('payment_type', 50)->nullable();
                $table->dateTime('waktu_transaksi');
                $table->dateTime('waktu_pembayaran')->nullable();
                $table->string('keterangan', 255)->default('SIMULASI PEMBAYARAN UTS');
                $table->text('payload_gateway')->nullable();
                $table->timestamps();

                $table->foreign('id_sppt')->references('id_sppt')->on('tagihan_sppt')->onDelete('cascade');
                $table->index('order_id');
                $table->index('status_pembayaran');
                $table->index('nop');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_simulasi_uts');
    }
};

