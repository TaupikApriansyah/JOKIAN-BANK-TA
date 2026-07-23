@extends('layouts.cs')
@section('title','Upload Arsip Digital')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-upload"></i> Upload Arsip Digital
            </div>
            <div class="page-subtitle">Tambahkan dokumen arsip ke sistem <b>SIBERKAS</b>.</div>
        </div>
    </div>

    <div class="card-form">
        @if ($errors->any())
            <div class="alert alert-danger mb-6" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Data belum bisa disimpan.</strong>
                    <span class="block text-xs font-medium">Cek kembali kolom yang diberi tanda merah.</span>
                </div>
            </div>
        @endif

        <form action="{{ route('cs.arsip.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="berkas_id" class="form-label">Pilih Berkas <span class="required">*</span></label>
                <select id="berkas_id" name="berkas_id" class="form-select {{ $errors->has('berkas_id') ? 'is-invalid' : '' }}" required>
                    <option value="">-- Pilih Berkas --</option>
                    @foreach($berkasList as $item)
                        <option value="{{ $item->id }}" {{ old('berkas_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->jenis_layanan }}{{ $item->nasabah ? ' — ' . $item->nasabah->nama_nasabah : '' }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text">Pilih berkas yang akan diarsipkan.</small>
                @error('berkas_id')<p class="invalid-feedback">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label for="jenis_dokumen" class="form-label">Jenis Dokumen <span class="required">*</span></label>
                <input
                    id="jenis_dokumen"
                    type="text"
                    name="jenis_dokumen"
                    class="form-control {{ $errors->has('jenis_dokumen') ? 'is-invalid' : '' }}"
                    value="{{ old('jenis_dokumen') }}"
                    placeholder="Contoh: KTP / NPWP / Sertifikat"
                    required
                >
                <small class="form-text">Sebutkan jenis dokumen yang diunggah.</small>
                @error('jenis_dokumen')<p class="invalid-feedback">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Upload File <span class="required">*</span></label>
                <x-ui.upload-field
                    name="file"
                    id="arsip_file"
                    :required="true"
                    help="PDF, JPG, PNG, DOC, atau DOCX • maks. 10 MB"
                />
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="bi bi-save-fill"></i>
                    Simpan Arsip
                </button>
                <a href="{{ route('cs.arsip.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
