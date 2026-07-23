@extends('layouts.cs')
@section('title','Tambah Transaksi | SIBERKAS')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-cash-coin"></i>
            Tambah Transaksi
        </div>
        <div class="page-subtitle">
            Silakan pilih berkas dan isi form transaksi
        </div>
    </div>

    <div class="card-form">

        {{-- ERROR MESSAGES --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-custom">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('cs.transaksi.store') }}" method="POST">
            @csrf

            {{-- PILIH BERKAS --}}
            <div class="mb-3">
                <label class="form-label">
                    Pilih Berkas <span class="required">*</span>
                </label>
                <select name="id_berkas" class="form-select" required>
    <option value="">-- Pilih Berkas --</option>
    @foreach($berkasList as $b)
        <option value="{{ $b->id }}" {{ old('id_berkas') == $b->id ? 'selected' : '' }}>
            {{ $b->jenis_layanan ?? 'Tanpa Layanan' }} - {{ $b->status_berkas ?? 'Status' }}
        </option>
    @endforeach
</select>

            {{-- TANGGAL TRANSAKSI --}}
            <div class="mb-3">
                <label class="form-label">
                    Tanggal Transaksi <span class="required">*</span>
                </label>
                <input type="date"
                       name="tanggal_transaksi"
                       value="{{ old('tanggal_transaksi', date('Y-m-d')) }}"
                       class="form-control"
                       required>
            </div>

            {{-- JENIS TRANSAKSI --}}
            <div class="mb-3">
                <label class="form-label">
                    Jenis Transaksi <span class="required">*</span>
                </label>
                <input type="text"
                       name="jenis_transaksi"
                       value="{{ old('jenis_transaksi') }}"
                       class="form-control"
                       placeholder="Contoh: Biaya Administrasi, Biaya Notaris, dll"
                       required>
            </div>

            {{-- NOMINAL --}}
            <div class="mb-3">
                <label class="form-label">
                    Nominal <span class="required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl rounded-r-none">Rp</span>
                    <input type="number"
                           name="nominal"
                           value="{{ old('nominal') }}"
                           class="form-control"
                           placeholder="0"
                           min="0"
                           step="0.01"
                           class="rounded-l-none rounded-r-xl"
                           required>
                </div>
                <small class="text-muted">Masukkan nominal dalam Rupiah</small>
            </div>

            {{-- KETERANGAN --}}
            <div class="mb-4">
                <label class="form-label">Keterangan (Opsional)</label>
                <textarea name="keterangan"
                          class="form-control"
                          rows="3"
                          placeholder="Tambahkan catatan atau keterangan tambahan...">{{ old('keterangan') }}</textarea>
            </div>

            {{-- BUTTONS --}}
            <div class="d-flex gap-3">
                <button type="submit" class="btn-save">
                    <i class="bi bi-save-fill"></i>
                    Simpan Transaksi
                </button>

                <a href="{{ route('cs.transaksi.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>

        </form>
    </div>

</div>

@endsection