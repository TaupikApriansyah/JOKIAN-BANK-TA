<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number')->unique();
            $table->foreignId('administrative_transaction_id')->constrained()->cascadeOnDelete();
            $table->string('debit_account');
            $table->string('credit_account');
            $table->decimal('amount', 18, 2);
            $table->enum('entry_type', ['normal', 'reversal'])->default('normal');
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('posted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
