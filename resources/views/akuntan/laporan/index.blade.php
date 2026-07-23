@extends('layouts.akuntan')
@section('title', 'Laporan Akuntansi')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div><h1 class="page-title"><i class="bi bi-file-earmark-bar-graph"></i>Laporan Akuntansi</h1><p class="page-subtitle">Laporan transaksi administratif yang telah diposting oleh Akuntan.</p></div>
        <div class="action-buttons"><a class="btn-download" href="{{ route('akuntan.laporan.excel', request()->query()) }}"><i class="bi bi-file-earmark-spreadsheet"></i>Unduh Excel</a><a class="btn-detail" href="{{ route('akuntan.laporan.pdf', request()->query()) }}" download><i class="bi bi-printer"></i>Unduh PDF</a></div>
    </header>

    <section class="report-filter">
        <form class="report-filter__form" method="GET" action="{{ route('akuntan.laporan.index') }}">
            <label class="form-group"><span class="form-label">Tanggal Mulai</span><input class="form-control" type="date" name="tanggal_mulai" value="{{ $start }}"></label>
            <label class="form-group"><span class="form-label">Tanggal Selesai</span><input class="form-control" type="date" name="tanggal_selesai" value="{{ $end }}"></label>
            <label class="form-group"><span class="form-label">Status</span><select class="form-select" name="status"><option value="Diposting" @selected($status === 'Diposting')>Diposting</option><option value="Semua" @selected($status === 'Semua')>Semua status</option></select></label>
            <div class="report-filter__actions"><button class="btn-search"><i class="bi bi-funnel"></i>Tampilkan</button><a class="btn-back" href="{{ route('akuntan.laporan.index') }}">Reset</a></div>
        </form>
        <p class="report-filter__note"><i class="bi bi-info-circle"></i>Periode aktif: <b>{{ $periodeLabel }}</b>. Nilai ringkasan dihitung dari transaksi berstatus <b>Diposting</b>.</p>
    </section>

    <section class="report-summary-grid report-summary-grid--three">
        <article class="report-summary"><span class="report-summary__icon"><i class="bi bi-arrow-down-circle"></i></span><div><span class="report-summary__label">Pemasukan</span><b class="report-summary__value">Rp {{ number_format($pemasukan, 0, ',', '.') }}</b><small>Transaksi diposting</small></div></article>
        <article class="report-summary report-summary--warn"><span class="report-summary__icon"><i class="bi bi-arrow-up-circle"></i></span><div><span class="report-summary__label">Pengeluaran</span><b class="report-summary__value">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</b><small>Transaksi diposting</small></div></article>
        <article class="report-summary report-summary--accent"><span class="report-summary__icon"><i class="bi bi-calculator"></i></span><div><span class="report-summary__label">Saldo Administratif</span><b class="report-summary__value">Rp {{ number_format($saldo, 0, ',', '.') }}</b><small>Pemasukan dikurangi pengeluaran</small></div></article>
    </section>

    <section class="dashboard-panel piutang-panel">
        <header class="dashboard-panel__head"><h2 class="dashboard-panel__title"><i class="bi bi-hourglass-split"></i>Piutang Administratif</h2><b class="piutang-panel__amount">Rp {{ number_format($piutangTotal, 0, ',', '.') }}</b></header>
        <div class="dashboard-panel__body">
            @forelse($piutang->take(5) as $item)
                <div class="sla-item"><div><b class="sla-item__name">{{ $item->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><span class="sla-item__sub">{{ $item->kategori }} · {{ $item->berkas?->jenis_layanan ?? '-' }}</span></div><b>Rp {{ number_format($item->nominal, 0, ',', '.') }}</b></div>
            @empty
                <p class="text-muted small">Tidak ada transaksi pemasukan yang masih belum dibayar atau menunggu verifikasi.</p>
            @endforelse
        </div>
    </section>

    <section class="table-wrapper report-table"><header class="table-title"><div><h2>Detail Transaksi</h2><p>Daftar transaksi berdasarkan filter laporan.</p></div><span class="table-title__count">{{ $transactions->count() }} transaksi</span></header><table class="table table-hover"><thead><tr><th>Tanggal</th><th>Nasabah / Berkas</th><th>Arah</th><th>Kategori</th><th>Nominal</th><th>Status</th></tr></thead><tbody>@forelse($transactions as $transaction)<tr><td>{{ optional($transaction->tanggal_transaksi)->format('d M Y') }}</td><td><b>{{ $transaction->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><small class="d-block text-muted">{{ $transaction->berkas?->jenis_layanan ?? '-' }}</small></td><td><span class="transaction-type transaction-type--{{ strtolower($transaction->arah_transaksi ?? 'pemasukan') }}">{{ $transaction->arah_transaksi }}</span></td><td>{{ $transaction->kategori }}<small class="d-block text-muted">{{ $transaction->jenis_transaksi }}</small></td><td class="fw-bold">Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</td><td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($transaction->status_transaksi) }}">{{ $transaction->status_transaksi }}</span></td></tr>@empty<tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada transaksi pada periode ini.</td></tr>@endforelse</tbody></table></section>
</div>
@endsection
