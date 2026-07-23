<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            if (!Schema::hasColumn('nasabah', 'created_by')) {
                $table->foreignId('created_by')
                      ->after('id')
                      ->constrained('users')
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            if (Schema::hasColumn('nasabah', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
