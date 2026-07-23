@extends('layouts.cs')
@section('title', 'Detail Nasabah')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-person-vcard-fill"></i>{{ $nasabah->nama_nasabah }}</h1><p class="page-subtitle">Identitas dan riwayat transaksi per nasabah.</p></div><a class="btn-back" href="{{ route('cs.nasabah.index') }}"><i class="bi bi-arrow-left"></i>Kembali</a></header>
    <section class="dashboard-grid">
        <article class="dashboard-panel"><header class="dashboard-panel__head"><h2 class="dashboard-panel__title"><i class="bi bi-person"></i>Identitas</h2></header><div class="dashboard-panel__body"><div class="detail-grid"><div class="detail-item"><span class="detail-label">NIK</span><b class="detail-value">{{ $nasabah->nik }}</b></div><div class="detail-item"><span class="detail-label">No. Telepon</span><b class="detail-value">{{ $nasabah->no_telepon }}</b></div><div class="detail-item span-2"><span class="detail-label">Alamat</span><b class="detail-value">{{ $nasabah->alamat }}</b></div></div></div></article>
        <article class="dashboard-panel"><header class="dashboard-panel__head"><h2 class="dashboard-panel__title"><i class="bi bi-folder2-open"></i>Berkas</h2></header><div class="dashboard-panel__body"><b class="metric__value">{{ $nasabah->berkas->count() }}</b><p class="metric__text">Berkas yang terhubung dengan nasabah ini.</p></div></article>
    </section>
    <section class="dashboard-panel" style="margin-top:1rem"><header class="dashboard-panel__head"><h2 class="dashboard-panel__title"><i class="bi bi-clock-history"></i>Riwayat Transaksi Nasabah</h2></header><div class="table-responsive"><table class="table"><thead><tr><th>Tanggal</th><th>Berkas</th><th>Kategori</th><th>Jenis</th><th>Status</th><th>Nominal</th></tr></thead><tbody>@forelse($nasabah->berkas->flatMap->transaksis->sortByDesc('tanggal_transaksi') as $trx)<tr><td>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') }}</td><td>{{ $trx->berkas?->jenis_layanan ?? '-' }}</td><td>{{ $trx->kategori ?? 'Lainnya' }}</td><td>{{ $trx->jenis_transaksi }}</td><td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($trx->status_transaksi ?? 'menunggu') }}">{{ $trx->status_transaksi ?? 'Belum Dibayar' }}</span></td><td>Rp {{ number_format($trx->nominal,0,',','.') }}</td></tr>@empty<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi untuk nasabah ini.</td></tr>@endforelse</tbody></table></div></section>
</div>
@endsection
