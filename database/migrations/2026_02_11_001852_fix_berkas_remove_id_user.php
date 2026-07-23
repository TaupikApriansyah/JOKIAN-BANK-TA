<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            // 1. Drop foreign key dulu
            if (Schema::hasColumn('berkas', 'id_user')) {
                $table->dropForeign(['id_user']);
                $table->dropColumn('id_user');
            }
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable();
        });
    }
};
