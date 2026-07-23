<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tracking_status', 'id_berkas')) {
            Schema::table('tracking_status', function (Blueprint $table) {
                $table->dropColumn('id_berkas');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('tracking_status', 'id_berkas')) {
            Schema::table('tracking_status', function (Blueprint $table) {
                $table->unsignedBigInteger('id_berkas')->nullable();
            });
        }
    }
};
