@extends('layouts.cs')
@section('title','Detail Berkas')
@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-file-earmark-text-fill"></i>
                Detail Berkas
            </div>
            <div class="page-subtitle">Informasi lengkap & riwayat status berkas</div>
        </div>
        <a href="{{ route('cs.berkas.index') }}" class="btn-back">
            <i class="bi bi-arrow-left-circle-fill"></i>
            Kembali
        </a>
    </div>

    <div class="card-berkas mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <p><strong>Nasabah</strong><br>{{ $berkas->nasabah->nama_nasabah }}</p>
                <p><strong>Jenis Layanan</strong><br>{{ $berkas->jenis_layanan }}</p>
                <p><strong>Tanggal Masuk</strong><br>{{ $berkas->tanggal_masuk->format('d M Y') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Estimasi Selesai</strong><br>
                    {{ $berkas->estimasi_selesai ? $berkas->estimasi_selesai->format('d M Y') : '-' }}
                </p>
                <p><strong>Status</strong><br>
                    <span class="badge-status badge-{{ strtolower($berkas->status_berkas) }}">
                        {{ $berkas->status_berkas }}
                    </span>
                </p>
                <p><strong>Petugas</strong><br>{{ $berkas->user->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card-berkas">
        <h5 class="fw-bold mb-3">
            <i class="bi bi-clock-history me-2 text-success"></i>
            Riwayat Tracking
        </h5>

        <div class="timeline">
            @forelse($berkas->trackings as $t)
                <div class="timeline-item">
                    <strong>{{ $t->status }}</strong>
                    <div class="text-muted small">
                        {{ $t->tanggal_update->format('d M Y H:i') }} • {{ $t->user->name ?? '-' }}
                    </div>
                    <div class="mt-1">{{ $t->keterangan ?? '-' }}</div>
                </div>
            @empty
                <p class="text-muted">Belum ada riwayat status.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
