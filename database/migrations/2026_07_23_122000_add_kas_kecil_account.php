<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('akun_akuntansi')) {
            return;
        }

        DB::table('akun_akuntansi')->updateOrInsert(
            ['kode_akun' => '114'],
            [
                'nama_akun' => 'Kas Kecil',
                'kelompok' => 'Aset',
                'saldo_normal' => 'Debit',
                'status' => 'aktif',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        // Akun tidak dihapus saat rollback agar referensi dan histori tetap aman.
    }
};
