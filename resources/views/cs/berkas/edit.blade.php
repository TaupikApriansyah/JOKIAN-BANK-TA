@extends('layouts.cs')
@section('title','Update Status Berkas')
@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-pencil-square"></i> Update Status Berkas
        </div>
        <div class="page-subtitle">Sesuai kolom di index</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">

                <form action="{{ route('cs.berkas.updateStatus', $berkas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- NASABAH --}}
                    <div class="mb-3">
                        <label class="form-label">Nasabah</label>
                        <input type="text" class="form-control" 
                               value="{{ $berkas->nasabah->nama_nasabah }}" readonly>
                    </div>

                    {{-- JENIS LAYANAN --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Layanan</label>
                        <input type="text" class="form-control" 
                               value="{{ $berkas->jenis_layanan }}" readonly>
                    </div>

                    {{-- TANGGAL MASUK --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="text" class="form-control" 
                               value="{{ $berkas->tanggal_masuk->format('d M Y') }}" readonly>
                    </div>

                    {{-- ESTIMASI SELESAI --}}
                    <div class="mb-3">
                        <label class="form-label">Estimasi Selesai</label>
                        <input type="text" class="form-control" 
                               value="{{ optional($berkas->estimasi_selesai)->format('d M Y') }}" readonly>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label">Status Berkas</label>
                        <select name="status_berkas" class="form-control" required>
                            <option value="Diterima" {{ $berkas->status_berkas=='Diterima'?'selected':'' }}>Diterima</option>
                            <option value="Diproses" {{ $berkas->status_berkas=='Diproses'?'selected':'' }}>Diproses</option>
                            <option value="Selesai"  {{ $berkas->status_berkas=='Selesai'?'selected':'' }}>Selesai</option>
                        </select>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control"
                                  placeholder="Contoh: Berkas sedang diverifikasi..."></textarea>
                    </div>

                    <div class="d-flex gap-3">
                        <button class="btn-submit">
                            <i class="bi bi-save-fill"></i> Simpan Status
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
