<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_administrasi', 'arah_transaksi')) {
                $table->string('arah_transaksi')->default('Pemasukan')->after('jenis_transaksi');
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('nomor_referensi');
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'diperiksa_oleh')) {
                $table->unsignedBigInteger('diperiksa_oleh')->nullable()->after('bukti_pembayaran');
                $table->foreign('diperiksa_oleh')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'tanggal_verifikasi')) {
                $table->dateTime('tanggal_verifikasi')->nullable()->after('diperiksa_oleh');
            }
            if (!Schema::hasColumn('transaksi_administrasi', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('tanggal_verifikasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_administrasi', 'diperiksa_oleh')) {
                $table->dropForeign(['diperiksa_oleh']);
            }

            $columns = ['arah_transaksi', 'bukti_pembayaran', 'diperiksa_oleh', 'tanggal_verifikasi', 'catatan_verifikasi'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('transaksi_administrasi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
