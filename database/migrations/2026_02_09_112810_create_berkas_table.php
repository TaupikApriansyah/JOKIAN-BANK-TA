<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
       Schema::create('berkas', function (Blueprint $table) {
    $table->id(); // <- ini kolom PK = id
    $table->foreignId('id_nasabah')->constrained('nasabah')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('jenis_layanan');
    $table->date('tanggal_masuk');
    $table->date('estimasi_selesai')->nullable();
    $table->enum('status_berkas', ['Diterima','Diproses','Selesai'])->default('Diterima');
    $table->timestamps();
});

    }

    public function down(): void {
        Schema::dropIfExists('berkas');
    }
};
