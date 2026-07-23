@extends('layouts.cs')
@section('title','Edit Nasabah')
@section('content')
<div class="container-fluid">
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom">
            <li class="breadcrumb-item">
                <a href="{{ route('cs.nasabah.index') }}">
                    <i class="bi bi-people-fill"></i> Data Nasabah
                </a>
            </li>
            <li class="breadcrumb-item active">Edit Nasabah</li>
        </ol>
    </nav>

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-pencil-square"></i>
            Edit Data Nasabah
        </div>
        <div class="page-subtitle">Perbarui informasi nasabah yang sudah ada</div>
    </div>

    <!-- FORM CARD -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-header">
                    <div class="form-header-icon">
                        <i class="bi bi-clipboard2-data-fill"></i>
                    </div>
                    <div class="form-header-text">
                        <h5>Informasi Nasabah</h5>
                        <p>Update data sesuai kebutuhan</p>
                    </div>
                </div>

                <div class="warning-box">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div class="warning-box-content">
                        <strong>Perhatian!</strong>
                        <p>Pastikan data yang diubah sudah benar. Perubahan akan langsung tersimpan setelah klik tombol Update.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cs.nasabah.update', $nasabah->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- NAMA NASABAH -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-fill"></i>
                            Nama Nasabah
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-person-circle input-icon"></i>
                            <input type="text" 
                                   name="nama_nasabah" 
                                   class="form-control @error('nama_nasabah') is-invalid @enderror" 
                                   value="{{ old('nama_nasabah', $nasabah->nama_nasabah) }}"
                                   placeholder="Masukkan nama lengkap nasabah"
                                   required>
                        </div>
                        @error('nama_nasabah')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text">
                            <i class="bi bi-info-circle"></i>
                            Contoh: Ahmad Suryanto
                        </small>
                    </div>

                    <!-- NIK -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-card-text"></i>
                            NIK (Nomor Induk Kependudukan)
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-credit-card-2-front input-icon"></i>
                            <input type="text" 
                                   name="nik" 
                                   class="form-control @error('nik') is-invalid @enderror" 
                                   value="{{ old('nik', $nasabah->nik) }}"
                                   placeholder="Masukkan 16 digit NIK"
                                   maxlength="16"
                                   pattern="[0-9]{16}"
                                   required>
                        </div>
                        @error('nik')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text">
                            <i class="bi bi-info-circle"></i>
                            NIK terdiri dari 16 digit angka sesuai KTP
                        </small>
                    </div>

                    <!-- ALAMAT -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-geo-alt-fill"></i>
                            Alamat
                            <span class="required">*</span>
                        </label>
                        <textarea name="alamat" 
                                  class="form-control @error('alamat') is-invalid @enderror" 
                                  placeholder="Masukkan alamat lengkap nasabah"
                                  required>{{ old('alamat', $nasabah->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text">
                            <i class="bi bi-info-circle"></i>
                            Alamat sesuai KTP atau domisili
                        </small>
                    </div>

                    <!-- NO TELEPON -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-telephone-fill"></i>
                            No. Telepon
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-phone input-icon"></i>
                            <input type="tel" 
                                   name="no_telepon" 
                                   class="form-control @error('no_telepon') is-invalid @enderror" 
                                   value="{{ old('no_telepon', $nasabah->no_telepon) }}"
                                   placeholder="Contoh: 081234567890"
                                   pattern="[0-9]{10,13}"
                                   required>
                        </div>
                        @error('no_telepon')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                        <small class="form-text">
                            <i class="bi bi-info-circle"></i>
                            Nomor telepon aktif yang dapat dihubungi (10-13 digit)
                        </small>
                    </div>

                    <!-- FORM ACTIONS -->
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle-fill"></i>
                            Update Data
                        </button>
                        <a href="{{ route('cs.nasabah.index') }}" class="btn-cancel">
                            <i class="bi bi-x-circle-fill"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection