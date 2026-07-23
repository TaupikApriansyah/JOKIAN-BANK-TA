@extends('layouts.akuntan')
@section('title', 'Dashboard Akuntansi')

@section('content')
<div class="container-fluid">
    <section class="dashboard-hero dashboard-hero--accounting">
        <h1 class="dashboard-hero__title"><i class="bi bi-calculator"></i>Dashboard Akuntansi</h1>
        <p class="dashboard-hero__copy">Verifikasi transaksi layanan yang dibuat CS, lalu sistem membentuk jurnal umum secara otomatis.</p>
        <span class="dashboard-hero__meta"><i class="bi bi-calendar3"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
    </section>

    <section class="metric-grid">
        <article class="metric metric--warn"><div class="metric__top"><span class="metric__label">Perlu Verifikasi</span><span class="metric__icon"><i class="bi bi-patch-question"></i></span></div><b class="metric__value">{{ $pendingCount }}</b><p class="metric__text">Transaksi dari CS</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Diposting Hari Ini</span><span class="metric__icon"><i class="bi bi-journal-check"></i></span></div><b class="metric__value">{{ $postedToday }}</b><p class="metric__text">Jurnal berhasil dibentuk</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Pemasukan Bulan Ini</span><span class="metric__icon"><i class="bi bi-arrow-down-circle"></i></span></div><b class="metric__value metric__money">Rp {{ number_format($pemasukanBulan, 0, ',', '.') }}</b><p class="metric__text">Transaksi sudah diposting</p></article>
        <article class="metric metric--rose"><div class="metric__top"><span class="metric__label">Pengeluaran Bulan Ini</span><span class="metric__icon"><i class="bi bi-arrow-up-circle"></i></span></div><b class="metric__value metric__money">Rp {{ number_format($pengeluaranBulan, 0, ',', '.') }}</b><p class="metric__text">Transaksi sudah diposting</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Saldo Petty Cash</span><span class="metric__icon"><i class="bi bi-wallet2"></i></span></div><b class="metric__value metric__money">Rp {{ number_format($saldoKasKecil, 0, ',', '.') }}</b><p class="metric__text"><a href="{{ route('akuntan.kas-kecil.index') }}">Kelola kas kecil</a></p></article>
    </section>

    <section class="accounting-grid">
        <x-ui.dashboard-panel title="Transaksi Menunggu Verifikasi" icon="bi-patch-check" :link="route('akuntan.transaksi.index')" link-text="Buka verifikasi">
            <div class="sla-list">
                @forelse($pendingTransactions as $transaction)
                    <div class="sla-item">
                        <div>
                            <b class="sla-item__name">{{ $transaction->berkas?->nasabah?->nama_nasabah ?? '-' }}</b>
                            <span class="sla-item__sub">{{ $transaction->kategori }} · {{ optional($transaction->tanggal_transaksi)->format('d M Y') }}</span>
                        </div>
                        <b>Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</b>
                    </div>
                @empty
                    <p class="text-muted small">Belum ada transaksi yang perlu diverifikasi.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Grafik Pemasukan dan Pengeluaran" icon="bi-bar-chart-line">
            <div class="simple-bar-chart">
                @foreach($chart as $item)
                    @php
                        $incomeHeight = max(4, round(($item['income'] / $chartMax) * 100));
                        $expenseHeight = max(4, round(($item['expense'] / $chartMax) * 100));
                    @endphp
                    <div class="simple-bar-chart__item" title="{{ $item['label'] }} · Pemasukan Rp {{ number_format($item['income'], 0, ',', '.') }} · Pengeluaran Rp {{ number_format($item['expense'], 0, ',', '.') }}">
                        <div class="simple-bar-chart__bars"><i class="simple-bar-chart__bar simple-bar-chart__bar--income" style="height: {{ $incomeHeight }}%"></i><i class="simple-bar-chart__bar simple-bar-chart__bar--expense" style="height: {{ $expenseHeight }}%"></i></div>
                        <span>{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="chart-key"><span><i class="chart-key__dot chart-key__dot--income"></i>Pemasukan</span><span><i class="chart-key__dot chart-key__dot--expense"></i>Pengeluaran</span></div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Jurnal Terakhir" icon="bi-journal-text" :link="route('akuntan.jurnal.index')" link-text="Lihat jurnal">
            <div class="sla-list">
                @forelse($latestJournals as $jurnal)
                    <div class="sla-item">
                        <div>
                            <b class="sla-item__name">{{ $jurnal->nomor_jurnal }}</b>
                            <span class="sla-item__sub">{{ $jurnal->transaksi?->berkas?->nasabah?->nama_nasabah ?? '-' }} · {{ optional($jurnal->tanggal_jurnal)->format('d M Y') }}</span>
                        </div>
                        <span class="status-badge status-diposting">Diposting</span>
                    </div>
                @empty
                    <p class="text-muted small">Belum ada jurnal yang dibuat.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>
    </section>
</div>
@endsection
