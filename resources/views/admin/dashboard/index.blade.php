@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
@php($doneRate = $totalBerkas ? round(($berkasSelesai / $totalBerkas) * 100) : 0)
<div class="container-fluid">
    <section class="dashboard-hero">
        <h1 class="dashboard-hero__title"><i class="bi bi-shield-check"></i>Dashboard Administrator</h1>
        <p class="dashboard-hero__copy">Pantau layanan berkas, SLA, pengguna aktif, dan transaksi administratif yang telah diposting Akuntan.</p>
        <span class="dashboard-hero__meta"><i class="bi bi-calendar3"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
    </section>

    <section class="metric-grid">
        <article class="metric"><div class="metric__top"><span class="metric__label">User Aktif</span><span class="metric__icon"><i class="bi bi-people"></i></span></div><b class="metric__value">{{ $totalUserAktif }}</b><p class="metric__text">{{ $csAktifHariIni }} akun CS aktif</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Total Berkas</span><span class="metric__icon"><i class="bi bi-folder2-open"></i></span></div><b class="metric__value">{{ $totalBerkas }}</b><p class="metric__text">Berkas seluruh sistem</p></article>
        <article class="metric metric--warn"><div class="metric__top"><span class="metric__label">Menunggu Verifikasi</span><span class="metric__icon"><i class="bi bi-patch-question"></i></span></div><b class="metric__value">{{ $menungguVerifikasi }}</b><p class="metric__text">Perlu proses CS atau Akuntan</p></article>
        <article class="metric metric--rose"><div class="metric__top"><span class="metric__label">SLA Terlambat</span><span class="metric__icon"><i class="bi bi-alarm"></i></span></div><b class="metric__value">{{ $berkasTerlambat }}</b><p class="metric__text">Perlu intervensi</p></article>
    </section>

    <section class="dashboard-grid">
        <x-ui.dashboard-panel title="Monitoring SLA" icon="bi-bell-fill" :link="route('admin.monitoring.berkas')" link-text="Buka monitoring">
            <div class="sla-summary"><div class="sla-stat"><strong>{{ $slaHampir }}</strong><span>Hampir jatuh tempo</span></div><div class="sla-stat sla-stat--urgent"><strong>{{ $berkasTerlambat }}</strong><span>Melewati SLA</span></div></div>
            <div class="sla-list">
                @forelse($slaList as $berkas)
                    @php($days = now()->startOfDay()->diffInDays($berkas->estimasi_selesai, false))
                    <div class="sla-item"><div><b class="sla-item__name">{{ $berkas->nasabah?->nama_nasabah ?? '-' }}</b><span class="sla-item__sub">{{ $berkas->jenis_layanan }} · {{ optional($berkas->estimasi_selesai)->format('d M Y') }}</span></div><span class="sla-item__status {{ $days >= 0 ? 'is-soon' : '' }}">{{ $days < 0 ? 'Terlambat' : ($days === 0 ? 'Hari ini' : $days . ' hari') }}</span></div>
                @empty
                    <p class="text-muted small">Tidak ada SLA yang perlu diawasi saat ini.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Distribusi Status Berkas" icon="bi-pie-chart-fill">
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

        <x-ui.dashboard-panel title="Ringkasan Keuangan Bulan Ini" icon="bi-wallet2" :link="route('admin.laporan.index')" link-text="Buka laporan">
            <div class="financial-list">
                <div class="financial-list__row"><span><i class="bi bi-arrow-down-circle"></i>Pemasukan</span><b class="is-income">Rp {{ number_format($pemasukanBulan, 0, ',', '.') }}</b></div>
                <div class="financial-list__row"><span><i class="bi bi-arrow-up-circle"></i>Pengeluaran</span><b class="is-expense">Rp {{ number_format($pengeluaranBulan, 0, ',', '.') }}</b></div>
                <div class="financial-list__row financial-list__row--total"><span><i class="bi bi-calculator"></i>Saldo</span><b>Rp {{ number_format($pemasukanBulan - $pengeluaranBulan, 0, ',', '.') }}</b></div>
            </div>
            <div class="mini-note"><i class="bi bi-shield-check"></i>Nilai hanya dihitung dari transaksi yang telah diposting Akuntan.</div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Transaksi Terbaru" icon="bi-cash-stack">
            <div class="sla-list">
                @forelse($latestTransactions as $trx)
                    <div class="sla-item"><div><b class="sla-item__name">{{ $trx->kategori ?? 'Transaksi' }} · {{ $trx->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><span class="sla-item__sub">{{ $trx->jenis_transaksi }} · {{ optional($trx->tanggal_transaksi)->format('d M Y') }}</span></div><b>Rp {{ number_format($trx->nominal, 0, ',', '.') }}</b></div>
                @empty
                    <p class="text-muted small">Belum ada transaksi yang tercatat.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>
    </section>
</div>
@endsection
