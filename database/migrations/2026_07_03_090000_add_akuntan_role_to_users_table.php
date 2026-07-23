<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Project memakai MySQL/XAMPP. Kolom role awal berbentuk enum,
        // jadi perlu diperluas agar akun Akuntan bisa disimpan.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','cs','akuntan') NOT NULL DEFAULT 'cs'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'akuntan')->update(['role' => 'cs']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','cs') NOT NULL DEFAULT 'cs'");
        }
    }
};
