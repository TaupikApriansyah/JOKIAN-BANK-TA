@extends('layouts.cs')
@section('title','Tambah Berkas')
@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-folder-plus"></i>
            Tambah Berkas Baru
        </div>
        <div class="page-subtitle">Isi formulir untuk menambahkan berkas nasabah</div>
    </div>

    <!-- FORM CARD -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-header">
                    <div class="form-header-icon">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                    <div class="form-header-text">
                        <h5>Informasi Berkas</h5>
                        <p>Pastikan data berkas sesuai dengan nasabah</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cs.berkas.store') }}">
                    @csrf

                    <!-- NASABAH -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-fill"></i> Nasabah
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-people-fill input-icon"></i>
                            <select name="id_nasabah" class="form-control" required>
                                <option value="">-- Pilih Nasabah --</option>
                                @foreach($nasabah as $n)
                                    <option value="{{ $n->id }}">{{ $n->nama_nasabah }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- JENIS LAYANAN -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-briefcase-fill"></i> Jenis Layanan
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-ui-checks input-icon"></i>
                            <input type="text" name="jenis_layanan" class="form-control" placeholder="Contoh: Kredit, Tabungan, dll" required>
                        </div>
                    </div>

                    <!-- TANGGAL MASUK -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar-event-fill"></i> Tanggal Masuk
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar2-date-fill input-icon"></i>
                            <input type="date" name="tanggal_masuk" class="form-control" required>
                        </div>
                    </div>

                    <!-- ESTIMASI SELESAI -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-hourglass-split"></i> Estimasi Selesai
                        </label>
                        <div class="input-wrapper">
                            <i class="bi bi-calendar-check-fill input-icon"></i>
                            <input type="date" name="estimasi_selesai" class="form-control">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save-fill"></i> Simpan Berkas
                        </button>
                        <a href="{{ route('cs.berkas.index') }}" class="btn-cancel">
                            <i class="bi bi-x-circle-fill"></i> Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
