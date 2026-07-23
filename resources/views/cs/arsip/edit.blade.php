@extends('layouts.cs')
@section('title','Edit Arsip')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-folder2-open"></i> Edit Arsip
            </div>
            <div class="page-subtitle">Perbarui dokumen arsip digital dengan aman.</div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-card">
                @if ($errors->any())
                    <div class="alert alert-danger mb-6" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Perubahan belum bisa disimpan.</strong>
                            <span class="block text-xs font-medium">Cek kembali kolom yang diberi tanda merah.</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('cs.arsip.update', $arsip->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="jenis_dokumen" class="form-label">Jenis Dokumen <span class="required">*</span></label>
                        <input
                            id="jenis_dokumen"
                            type="text"
                            name="jenis_dokumen"
                            class="form-control {{ $errors->has('jenis_dokumen') ? 'is-invalid' : '' }}"
                            value="{{ old('jenis_dokumen', $arsip->jenis_dokumen) }}"
                            required
                        >
                        @error('jenis_dokumen')<p class="invalid-feedback">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ganti File <span class="text-slate-400">(opsional)</span></label>
                        <x-ui.upload-field
                            name="file"
                            id="arsip_file"
                            :required="false"
                            :current-file="$arsip->nama_file"
                            help="PDF, JPG, PNG, DOC, atau DOCX • maks. 10 MB"
                            optional-note="Biarkan kosong bila file saat ini tidak ingin diganti. Tombol tempat sampah hanya membatalkan pilihan file baru."
                        />
                    </div>

                    <div class="form-group">
                        <label for="tanggal_upload" class="form-label">Tanggal Upload <span class="required">*</span></label>
                        <input
                            id="tanggal_upload"
                            type="date"
                            name="tanggal_upload"
                            class="form-control {{ $errors->has('tanggal_upload') ? 'is-invalid' : '' }}"
                            value="{{ old('tanggal_upload', \Carbon\Carbon::parse($arsip->tanggal_upload)->format('Y-m-d')) }}"
                            required
                        >
                        @error('tanggal_upload')<p class="invalid-feedback">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-update">
                            <i class="bi bi-save-fill"></i>
                            Update Arsip
                        </button>
                        <a href="{{ route('cs.arsip.index') }}" class="btn-back">
                            <i class="bi bi-arrow-left-circle"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
