<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('cs','admin','accountant') NOT NULL DEFAULT 'cs'");
        }

        if (! Schema::hasColumn('journal_entries', 'status')) {
            Schema::table('journal_entries', function (Blueprint $table): void {
                $table->enum('status', ['draft', 'posted'])->default('posted')->index()->after('entry_type');
            });
        }

        if (! Schema::hasColumn('journal_entries', 'prepared_by')) {
            Schema::table('journal_entries', function (Blueprint $table): void {
                $table->foreignId('prepared_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('journal_entries', 'review_note')) {
            Schema::table('journal_entries', function (Blueprint $table): void {
                $table->text('review_note')->nullable()->after('posted_at');
            });
        }

        DB::table('journal_entries')
            ->whereNull('prepared_by')
            ->whereNotNull('posted_by')
            ->update(['prepared_by' => DB::raw('posted_by')]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE journal_entries MODIFY posted_by BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE journal_entries MODIFY posted_at TIMESTAMP NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET role = 'admin' WHERE role = 'accountant'");
            DB::statement("ALTER TABLE users MODIFY role ENUM('cs','admin') NOT NULL DEFAULT 'cs'");

            DB::statement('UPDATE journal_entries SET posted_by = COALESCE(posted_by, prepared_by), posted_at = COALESCE(posted_at, created_at) WHERE posted_by IS NULL OR posted_at IS NULL');
            DB::statement('ALTER TABLE journal_entries MODIFY posted_by BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE journal_entries MODIFY posted_at TIMESTAMP NOT NULL');
        }

        if (Schema::hasColumn('journal_entries', 'prepared_by')) {
            Schema::table('journal_entries', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('prepared_by');
            });
        }

        if (Schema::hasColumn('journal_entries', 'review_note')) {
            Schema::table('journal_entries', function (Blueprint $table): void {
                $table->dropColumn('review_note');
            });
        }

        if (Schema::hasColumn('journal_entries', 'status')) {
            Schema::table('journal_entries', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }
};
