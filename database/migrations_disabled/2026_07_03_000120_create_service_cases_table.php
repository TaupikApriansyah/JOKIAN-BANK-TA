<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_cases', function (Blueprint $table) {
            $table->id();
            $table->string('file_number')->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['baru', 'menunggu_dokumen', 'diproses', 'selesai', 'ditolak'])->default('baru')->index();
            $table->enum('sla_status', ['aman', 'mendekati', 'terlambat', 'selesai'])->default('aman')->index();
            $table->timestamp('received_at');
            $table->timestamp('due_at')->index();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_cases');
    }
};
