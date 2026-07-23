<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('arsip_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('jenis_dokumen');
            $table->string('path_file');
            $table->date('tanggal_upload');
            $table->enum('status_arsip', ['Aktif','Arsip'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('arsip_digital');
    }
};
