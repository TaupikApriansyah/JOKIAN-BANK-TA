<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_berkas')->after('id');

            $table->foreign('id_berkas')
                  ->references('id')     // 👉 ke berkas.id
                  ->on('berkas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_administrasi', function (Blueprint $table) {
            $table->dropForeign(['id_berkas']);
            $table->dropColumn('id_berkas');
        });
    }
};
