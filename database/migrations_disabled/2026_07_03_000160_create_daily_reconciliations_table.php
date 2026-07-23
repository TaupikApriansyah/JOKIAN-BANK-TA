<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->date('reconciliation_date')->unique();
            $table->decimal('system_total', 18, 2);
            $table->decimal('physical_total', 18, 2);
            $table->decimal('difference', 18, 2);
            $table->enum('status', ['sesuai', 'selisih_lebih', 'selisih_kurang', 'perlu_tindak_lanjut']);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reconciliations');
    }
};
