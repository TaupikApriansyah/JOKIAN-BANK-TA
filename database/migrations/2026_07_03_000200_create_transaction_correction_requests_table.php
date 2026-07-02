<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_correction_requests', function (Blueprint $table) {
            $table->id();

            // Explicit short constraint names are required because MySQL limits identifiers to 64 characters.
            $table->unsignedBigInteger('administrative_transaction_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->unsignedBigInteger('replacement_transaction_id')->nullable();

            $table->string('proposed_category');
            $table->string('proposed_payment_method');
            $table->decimal('proposed_amount', 18, 2);
            $table->text('proposed_description')->nullable();
            $table->text('reason');
            $table->string('supporting_path')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'disetujui', 'ditolak'])
                ->default('menunggu_verifikasi');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();

            $table->foreign('administrative_transaction_id', 'tcr_admin_tx_fk')
                ->references('id')
                ->on('administrative_transactions')
                ->restrictOnDelete();

            $table->foreign('requested_by', 'tcr_requested_by_fk')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            $table->foreign('reviewed_by', 'tcr_reviewed_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('replacement_transaction_id', 'tcr_replacement_tx_fk')
                ->references('id')
                ->on('administrative_transactions')
                ->nullOnDelete();

            $table->index('status', 'tcr_status_idx');
            $table->index(['administrative_transaction_id', 'status'], 'tcr_tx_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_correction_requests');
    }
};
