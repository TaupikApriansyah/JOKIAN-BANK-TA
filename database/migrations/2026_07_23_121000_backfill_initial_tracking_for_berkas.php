<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('berkas') || !Schema::hasTable('tracking_status')) {
            return;
        }

        DB::table('berkas')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('tracking_status')
                    ->whereColumn('tracking_status.berkas_id', 'berkas.id');
            })
            ->chunkById(100, function ($berkasItems) {
                $now = now();
                $rows = [];

                foreach ($berkasItems as $berkas) {
                    $rows[] = [
                        'berkas_id' => $berkas->id,
                        'user_id' => $berkas->user_id,
                        'status' => $berkas->status_berkas ?: 'Diterima',
                        'tanggal_update' => $berkas->created_at ?: $now,
                        'keterangan' => 'Riwayat awal dibuat otomatis saat pembaruan sistem.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows) {
                    DB::table('tracking_status')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        // Data tracking hasil backfill tidak dihapus saat rollback agar riwayat tetap aman.
    }
};
