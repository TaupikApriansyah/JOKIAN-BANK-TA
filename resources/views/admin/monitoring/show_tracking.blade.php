@extends('layouts.admin')
@section('title','Detail Tracking | SIBERKAS')
@section('content')
<div class="container-fluid">

<h4 class="mb-4 fw-bold">Detail Tracking Status</h4>

<div class="card-detail">

<div class="row">
<div class="col-md-6">
<div class="label">Nama Nasabah</div>
<div class="value">{{ $tracking->berkas->nasabah->nama_nasabah ?? '-' }}</div>
</div>

<div class="col-md-6">
<div class="label">Jenis Layanan</div>
<div class="value">{{ $tracking->berkas->jenis_layanan ?? '-' }}</div>
</div>

<div class="col-md-6">
<div class="label">Status</div>
<div class="value">{{ $tracking->status }}</div>
</div>

<div class="col-md-6">
<div class="label">Tanggal Update</div>
<div class="value">
{{ \Carbon\Carbon::parse($tracking->tanggal_update)->format('d M Y H:i') }}
</div>
</div>

<div class="col-md-12">
<div class="label">Diproses Oleh</div>
<div class="value">{{ $tracking->user->name ?? '-' }}</div>
</div>

</div>

<a href="{{ route('admin.monitoring.tracking') }}" class="btn btn-secondary mt-3">
← Kembali ke Monitoring Tracking
</a>

</div>
</div>

@endsection
