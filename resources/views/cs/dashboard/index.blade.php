@extends('layouts.cs')
@section('title', 'Dashboard CS')

@section('content')
@php($doneRate = $totalBerkas ? round(($berkasSelesai / $totalBerkas) * 100) : 0)
<div class="container-fluid">
    <section class="dashboard-hero">
        <h1 class="dashboard-hero__title"><i class="bi bi-person-workspace"></i>Dashboard Customer Service</h1>
        <p class="dashboard-hero__copy">Ringkasan berkas, transaksi, dan peringatan SLA dari data yang kamu kelola.</p>
        <span class="dashboard-hero__meta"><i class="bi bi-calendar3"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
    </section>

    <section class="metric-grid">
        <article class="metric"><div class="metric__top"><span class="metric__label">Nasabah</span><span class="metric__icon"><i class="bi bi-people"></i></span></div><b class="metric__value">{{ $totalNasabah }}</b><p class="metric__text">Data nasabah milikmu</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Berkas Aktif</span><span class="metric__icon"><i class="bi bi-folder2-open"></i></span></div><b class="metric__value">{{ $totalBerkas }}</b><p class="metric__text">Semua berkas tercatat</p></article>
        <article class="metric metric--warn"><div class="metric__top"><span class="metric__label">Diproses</span><span class="metric__icon"><i class="bi bi-arrow-repeat"></i></span></div><b class="metric__value">{{ $berkasDiproses }}</b><p class="metric__text">Masih dalam pengerjaan</p></article>
        <article class="metric metric--rose"><div class="metric__top"><span class="metric__label">SLA Terlambat</span><span class="metric__icon"><i class="bi bi-alarm"></i></span></div><b class="metric__value">{{ $berkasTerlambat }}</b><p class="metric__text">Perlu ditindaklanjuti</p></article>
    </section>

    <section class="dashboard-grid">
        <x-ui.dashboard-panel title="Peringatan SLA" icon="bi-bell-fill" :link="route('cs.berkas.index')" link-text="Buka berkas">
            <div class="sla-summary"><div class="sla-stat"><strong>{{ $slaHampir }}</strong><span>Hampir jatuh tempo</span></div><div class="sla-stat sla-stat--urgent"><strong>{{ $berkasTerlambat }}</strong><span>Sudah melewati SLA</span></div></div>
            <div class="sla-list">
                @forelse($slaList as $berkas)
                    @php($days = now()->startOfDay()->diffInDays($berkas->estimasi_selesai, false))
                    <div class="sla-item"><div><b class="sla-item__name">{{ $berkas->nasabah?->nama_nasabah ?? '-' }}</b><span class="sla-item__sub">{{ $berkas->jenis_layanan }} · {{ \Carbon\Carbon::parse($berkas->estimasi_selesai)->format('d M Y') }}</span></div><span class="sla-item__status {{ $days >= 0 ? 'is-soon' : '' }}">{{ $days < 0 ? 'Terlambat' : ($days === 0 ? 'Hari ini' : $days . ' hari') }}</span></div>
                @empty
                    <p class="text-muted small">Tidak ada berkas yang mendekati batas SLA.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Status Berkas" icon="bi-pie-chart-fill">
            <div class="chart-wrap">
                <div class="donut" style="--value: {{ $doneRate }}"><span class="donut__text">{{ $doneRate }}%</span></div>
                <div class="bar-list">
                    @foreach($statusSummary as $label => $value)
                        @php($percent = $totalBerkas ? round(($value / $totalBerkas) * 100) : 0)
                        <div class="bar-item"><span>{{ $label }}</span><span class="bar-line"><span style="width: {{ $percent }}%"></span></span><b>{{ $value }}</b></div>
                    @endforeach
                </div>
            </div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Transaksi Terbaru" icon="bi-receipt-cutoff" :link="route('cs.transaksi.index')" link-text="Kelola transaksi">
            <div class="info-box"><i class="bi bi-wallet2"></i><span>Total nominal: <b>Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</b></span></div>
            <div class="sla-list">
                @forelse($latestTransactions as $trx)
                    <div class="sla-item"><div><b class="sla-item__name">{{ $trx->kategori ?? 'Transaksi' }} · {{ $trx->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><span class="sla-item__sub">{{ $trx->jenis_transaksi }} · {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y') }}</span></div><b>Rp {{ number_format($trx->nominal,0,',','.') }}</b></div>
                @empty
                    <p class="text-muted small">Belum ada transaksi yang dicatat.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>
    </section>
</div>
@endsection
