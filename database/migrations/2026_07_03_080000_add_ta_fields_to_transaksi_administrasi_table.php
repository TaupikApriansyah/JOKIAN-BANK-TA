<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_administrasi', 'kategori')) {
                $table->string('kategori')->default('Lainnya')->after('jenis_transaksi');
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'status_transaksi')) {
                $table->string('status_transaksi')->default('Belum Dibayar')->after('nominal');
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->default('Tunai')->after('status_transaksi');
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'nomor_referensi')) {
                $table->string('nomor_referensi')->nullable()->after('metode_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            $columns = ['kategori', 'status_transaksi', 'metode_pembayaran', 'nomor_referensi'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('transaksi_administrasi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
