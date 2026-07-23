<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_kecil', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->enum('jenis', ['Masuk', 'Keluar'])->index();
            $table->string('kategori', 100);
            $table->string('keterangan', 255);
            $table->decimal('nominal', 15, 2);
            $table->string('nomor_bukti', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_kecil');
    }
};
