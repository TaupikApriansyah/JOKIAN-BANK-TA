<?php

namespace Database\Seeders;

use App\Models\AkunAkuntansi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'admin@gmail.com', 'name' => 'Administrator', 'password' => 'admin123', 'role' => 'admin'],
            ['email' => 'cs@gmail.com', 'name' => 'Customer Service', 'password' => 'cs123', 'role' => 'cs'],
            ['email' => 'akuntan@gmail.com', 'name' => 'Petugas Akuntansi', 'password' => 'akuntan123', 'role' => 'akuntan'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role' => $user['role'],
                    'status' => 'aktif',
                ]
            );
        }

        $accounts = [
            ['kode_akun' => '111', 'nama_akun' => 'Kas', 'kelompok' => 'Aset', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '112', 'nama_akun' => 'Bank', 'kelompok' => 'Aset', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '113', 'nama_akun' => 'Piutang Administrasi', 'kelompok' => 'Aset', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '114', 'nama_akun' => 'Kas Kecil', 'kelompok' => 'Aset', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '411', 'nama_akun' => 'Pendapatan Administrasi', 'kelompok' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '412', 'nama_akun' => 'Pendapatan Layanan', 'kelompok' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '511', 'nama_akun' => 'Beban Operasional', 'kelompok' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '512', 'nama_akun' => 'Beban ATK dan Cetak', 'kelompok' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '513', 'nama_akun' => 'Beban Transportasi', 'kelompok' => 'Beban', 'saldo_normal' => 'Debit'],
        ];

        foreach ($accounts as $account) {
            AkunAkuntansi::updateOrCreate(
                ['kode_akun' => $account['kode_akun']],
                $account + ['status' => 'aktif']
            );
        }
    }
}
