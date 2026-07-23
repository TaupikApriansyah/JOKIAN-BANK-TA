@extends('layouts.cs')

@section('title','Detail Tracking Berkas')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <div class="page-title">
                <i class="bi bi-file-earmark-text-fill"></i>
                Detail Berkas & Tracking
            </div>
            <div class="page-subtitle">
                Informasi lengkap & histori status
            </div>
        </div>

        <a href="{{ route('cs.tracking.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- DATA BERKAS --}}
    <div class="card-berkas mb-4 p-4">
        <h5 class="fw-bold mb-3">📄 Data Berkas</h5>
        <table class="table">
            <tr>
                <th>Nasabah</th>
                <td>{{ $tracking->berkas->nasabah->nama_nasabah ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Layanan</th>
                <td>{{ $tracking->berkas->jenis_layanan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Masuk</th>
                <td>{{ $tracking->berkas->tanggal_masuk?->format('d M Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Estimasi Selesai</th>
                <td>{{ $tracking->berkas->estimasi_selesai?->format('d M Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status Berkas</th>
                <td>
                    <span class="badge-status badge-{{ strtolower($tracking->berkas->status_berkas ?? 'diproses') }}">
                        {{ $tracking->berkas->status_berkas ?? '-' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- RIWAYAT TRACKING --}}
    <div class="card-berkas p-4">
        <h5 class="fw-bold mb-3">🕘 Riwayat Tracking</h5>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tracking->berkas->trackings as $t)
                    <tr>
                        <td>{{ $t->tanggal_update?->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge-status badge-{{ strtolower($t->status) }}">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td>{{ $t->keterangan ?? '-' }}</td>
                        <td>{{ $t->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Belum ada histori
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
