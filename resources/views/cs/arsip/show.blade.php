@extends('layouts.cs')
@section('title','Detail Arsip')
@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-archive-fill"></i> Detail Arsip
        </div>
    </div>

    <div class="card-detail">

        <div class="detail-item">
            <div class="detail-label">ID Berkas</div>
            <div class="detail-value">{{ $arsip->id_berkas }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Nama File</div>
            <div class="detail-value">{{ $arsip->nama_file }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Jenis Dokumen</div>
            <div class="detail-value">{{ $arsip->jenis_dokumen }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Tanggal Upload</div>
            <div class="detail-value">
                {{ \Carbon\Carbon::parse($arsip->tanggal_upload)->format('d M Y') }}
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span class="badge-status">{{ $arsip->status_arsip }}</span>
            </div>
        </div>

        <div class="mt-4 d-flex gap-3">
            <a href="{{ route('cs.arsip.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            <a href="{{ route('cs.arsip.download',$arsip->id_arsip) }}" class="btn-download">
                <i class="bi bi-download"></i> Download
            </a>
        </div>

    </div>

</div>

@endsection
