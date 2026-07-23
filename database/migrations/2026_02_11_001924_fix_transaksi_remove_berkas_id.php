<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_administrasi', 'berkas_id')) {
                // 1. Drop foreign key dulu
                $table->dropForeign(['berkas_id']);

                // 2. Baru drop kolom
                $table->dropColumn('berkas_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            $table->unsignedBigInteger('berkas_id')->nullable();
        });
    }
};
