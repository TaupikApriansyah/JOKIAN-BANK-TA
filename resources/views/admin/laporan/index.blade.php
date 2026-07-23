@extends('layouts.admin')
@section('title', 'Laporan Sistem')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-file-earmark-bar-graph"></i>Laporan Sistem</h1>
            <p class="page-subtitle">Ringkasan berkas, monitoring SLA, serta transaksi administratif yang sudah diposting Akuntan.</p>
        </div>
        <div class="action-buttons">
            <a class="btn-download" href="{{ route('admin.laporan.excel', request()->query()) }}"><i class="bi bi-file-earmark-spreadsheet"></i>Unduh Excel</a>
            <a class="btn-detail" href="{{ route('admin.laporan.pdf', request()->query()) }}" download><i class="bi bi-printer"></i>Unduh PDF</a>
        </div>
    </header>

    <x-ui.flash />

    <section class="report-filter">
        <form class="report-filter__form" method="GET" action="{{ route('admin.laporan.index') }}">
            <label class="form-group"><span class="form-label">Tanggal Mulai</span><input class="form-control" type="date" name="tanggal_mulai" value="{{ $start }}"></label>
            <label class="form-group"><span class="form-label">Tanggal Selesai</span><input class="form-control" type="date" name="tanggal_selesai" value="{{ $end }}"></label>
            <label class="form-group"><span class="form-label">Status Transaksi</span><select class="form-select" name="status"><option value="Semua" @selected($status === 'Semua')>Semua status</option><option value="Belum Dibayar" @selected($status === 'Belum Dibayar')>Belum Dibayar</option><option value="Menunggu Verifikasi" @selected($status === 'Menunggu Verifikasi')>Menunggu Verifikasi</option><option value="Diposting" @selected($status === 'Diposting')>Diposting</option><option value="Ditolak" @selected($status === 'Ditolak')>Ditolak</option></select></label>
            <div class="report-filter__actions"><button class="btn-search" type="submit"><i class="bi bi-funnel"></i>Tampilkan</button><a class="btn-back" href="{{ route('admin.laporan.index') }}">Reset</a></div>
        </form>
        <p class="report-filter__note"><i class="bi bi-info-circle"></i>Periode laporan: <b>{{ $periodeLabel }}</b>. Ringkasan keuangan hanya memakai transaksi berstatus <b>Diposting</b>.</p>
    </section>

    <section class="report-summary-grid">
        <article class="report-summary"><span class="report-summary__icon"><i class="bi bi-people"></i></span><div><span class="report-summary__label">Nasabah Terdaftar</span><b class="report-summary__value">{{ $totalNasabah }}</b><small>Seluruh nasabah di sistem</small></div></article>
        <article class="report-summary"><span class="report-summary__icon"><i class="bi bi-folder2-open"></i></span><div><span class="report-summary__label">Berkas Selesai</span><b class="report-summary__value">{{ $berkasSelesai }}<em>/{{ $totalBerkas }}</em></b><small>Berdasarkan periode berkas masuk</small></div></article>
        <article class="report-summary report-summary--warn"><span class="report-summary__icon"><i class="bi bi-alarm"></i></span><div><span class="report-summary__label">SLA Perlu Tindak Lanjut</span><b class="report-summary__value">{{ $slaHampir + $slaTerlambat }}</b><small>{{ $slaTerlambat }} terlambat · {{ $slaHampir }} hampir</small></div></article>
        <article class="report-summary report-summary--accent"><span class="report-summary__icon"><i class="bi bi-hourglass-split"></i></span><div><span class="report-summary__label">Menunggu Verifikasi</span><b class="report-summary__value">{{ $menungguVerifikasi }}</b><small>Perlu proses CS atau Akuntan</small></div></article>
    </section>

    <section class="report-layout">
        <x-ui.dashboard-panel title="Ringkasan Transaksi Diposting" icon="bi-wallet2">
            <div class="financial-list">
                <div class="financial-list__row"><span><i class="bi bi-arrow-down-circle"></i>Pemasukan</span><b class="is-income">Rp {{ number_format($pemasukan, 0, ',', '.') }}</b></div>
                <div class="financial-list__row"><span><i class="bi bi-arrow-up-circle"></i>Pengeluaran</span><b class="is-expense">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</b></div>
                <div class="financial-list__row financial-list__row--total"><span><i class="bi bi-calculator"></i>Saldo Administratif</span><b>Rp {{ number_format($saldo, 0, ',', '.') }}</b></div>
            </div>
            <div class="mini-note"><i class="bi bi-shield-check"></i>Admin memantau laporan. Posting dan jurnal dilakukan oleh Akuntan.</div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Grafik Transaksi Diposting" icon="bi-bar-chart-line">
            <div class="simple-bar-chart">
                @foreach($monthlySummary as $row)
                    @php
                        $incomeHeight = max(4, round(($row['pemasukan'] / $monthlyMax) * 100));
                        $expenseHeight = max(4, round(($row['pengeluaran'] / $monthlyMax) * 100));
                    @endphp
                    <div class="simple-bar-chart__item" title="{{ $row['label'] }} · Pemasukan Rp {{ number_format($row['pemasukan'], 0, ',', '.') }} · Pengeluaran Rp {{ number_format($row['pengeluaran'], 0, ',', '.') }}"><div class="simple-bar-chart__bars"><i class="simple-bar-chart__bar simple-bar-chart__bar--income" style="height: {{ $incomeHeight }}%"></i><i class="simple-bar-chart__bar simple-bar-chart__bar--expense" style="height: {{ $expenseHeight }}%"></i></div><span>{{ $row['label'] }}</span></div>
                @endforeach
            </div>
            <div class="chart-key"><span><i class="chart-key__dot chart-key__dot--income"></i>Pemasukan</span><span><i class="chart-key__dot chart-key__dot--expense"></i>Pengeluaran</span></div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Kategori Transaksi Diposting" icon="bi-tags-fill">
            <div class="category-list">
                @forelse($categorySummary->take(5) as $category => $amount)
                    <div class="category-list__item"><span>{{ $category }}</span><b>Rp {{ number_format($amount, 0, ',', '.') }}</b></div>
                @empty
                    <p class="text-muted small">Belum ada transaksi yang diposting pada periode ini.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>

        <x-ui.dashboard-panel title="Alert SLA Saat Ini" icon="bi-bell-fill">
            <div class="sla-list">
                @forelse($slaList as $berkas)
                    @php($days = now()->startOfDay()->diffInDays($berkas->estimasi_selesai, false))
                    <div class="sla-item"><div><b class="sla-item__name">{{ $berkas->nasabah?->nama_nasabah ?? '-' }}</b><span class="sla-item__sub">{{ $berkas->jenis_layanan }} · {{ optional($berkas->estimasi_selesai)->format('d M Y') }}</span></div><span class="sla-item__status {{ $days >= 0 ? 'is-soon' : '' }}">{{ $days < 0 ? 'Terlambat' : ($days === 0 ? 'Hari ini' : $days . ' hari') }}</span></div>
                @empty
                    <p class="text-muted small">Tidak ada SLA yang perlu ditindaklanjuti.</p>
                @endforelse
            </div>
        </x-ui.dashboard-panel>
    </section>

    <section class="table-wrapper report-table">
        <header class="table-title"><div><h2>Detail Transaksi</h2><p>Data transaksi sesuai filter yang dipilih.</p></div><span class="table-title__count">{{ $transactions->count() }} transaksi</span></header>
        <table class="table table-hover">
            <thead><tr><th>Tanggal</th><th>Nasabah / Berkas</th><th>Arah</th><th>Kategori</th><th>Nominal</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ optional($transaction->tanggal_transaksi)->format('d M Y') }}</td>
                    <td><b>{{ $transaction->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><small class="d-block text-muted">{{ $transaction->berkas?->jenis_layanan ?? '-' }}</small></td>
                    <td><span class="transaction-type transaction-type--{{ strtolower($transaction->arah_transaksi ?? 'pemasukan') }}">{{ $transaction->arah_transaksi }}</span></td>
                    <td>{{ $transaction->kategori }}<small class="d-block text-muted">{{ $transaction->jenis_transaksi }}</small></td>
                    <td class="fw-bold">Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</td>
                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($transaction->status_transaksi) }}">{{ $transaction->status_transaksi }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada transaksi pada filter tersebut.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
