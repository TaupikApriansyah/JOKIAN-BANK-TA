@extends('layouts.admin')
@section('title','Detail Berkas | SIBERKAS')

@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-file-earmark-text"></i>
            Detail Data Berkas
        </div>
        <div class="page-subtitle">
            Informasi lengkap berkas (Read Only)
        </div>
    </div>

    <div class="card-detail">

        @php
            $statusClass = match($berkas->status_berkas){
                'Diterima' => 'status-diterima',
                'Diproses' => 'status-diproses',
                'Selesai'  => 'status-selesai',
                'Ditolak'  => 'status-ditolak',
                default    => 'status-diterima'
            };
        @endphp

        <div class="row">

            <div class="col-md-6 detail-box">
                <div class="label">Nama Nasabah</div>
                <div class="value">
                    {{ $berkas->nasabah->nama_nasabah ?? '-' }}
                </div>
            </div>

            <div class="col-md-6 detail-box">
                <div class="label">Jenis Layanan</div>
                <div class="value">
                    {{ $berkas->jenis_layanan }}
                </div>
            </div>

            <div class="col-md-6 detail-box">
                <div class="label">Tanggal Masuk</div>
                <div class="value">
                    {{ \Carbon\Carbon::parse($berkas->tanggal_masuk)->format('d M Y') }}
                </div>
            </div>

            <div class="col-md-6 detail-box">
                <div class="label">Status Berkas</div>
                <div class="value">
                    <span class="status-badge {{ $statusClass }}">
                        {{ $berkas->status_berkas }}
                    </span>
                </div>
            </div>

            <div class="col-md-6 detail-box">
                <div class="label">Customer Service</div>
                <div class="value">
                    {{ $berkas->user->name ?? '-' }}
                </div>
            </div>


        </div>

        <div class="mt-4">
            <a href="{{ route('admin.monitoring.berkas') }}" class="btn-back">
                ← Kembali ke Monitoring
            </a>
        </div>

    </div>

</div>

@endsection
