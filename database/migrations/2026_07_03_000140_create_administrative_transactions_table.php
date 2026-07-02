<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('service_case_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('corrected_from_id')->nullable()->constrained('administrative_transactions')->nullOnDelete();
            $table->string('category');
            $table->string('payment_method');
            $table->decimal('amount', 18, 2);
            $table->string('debit_account')->default('101.00.00 Kas (Teller)');
            $table->string('credit_account')->default('401.05.00 Pendapatan Biaya Administrasi');
            $table->text('description')->nullable();
            $table->string('proof_path')->nullable();
            $table->enum('status', ['draft', 'menunggu_verifikasi', 'disetujui', 'dikembalikan', 'ditolak', 'dibatalkan', 'dikoreksi'])->default('draft')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_note')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_transactions');
    }
};
