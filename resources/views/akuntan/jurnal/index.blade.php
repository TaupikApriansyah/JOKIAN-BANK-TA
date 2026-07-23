@extends('layouts.akuntan')
@section('title', 'Jurnal Umum')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div><h1 class="page-title"><i class="bi bi-journal-text"></i>Jurnal Umum</h1><p class="page-subtitle">Jurnal dibuat otomatis ketika transaksi layanan berhasil diverifikasi dan diposting.</p></div>
        <a class="btn-detail" href="{{ route('akuntan.jurnal.ledger') }}"><i class="bi bi-table"></i>Buka Buku Besar</a>
    </header>

    <section class="report-filter">
        <form class="report-filter__form" method="GET" action="{{ route('akuntan.jurnal.index') }}">
            <label class="form-group"><span class="form-label">Tanggal Mulai</span><input class="form-control" type="date" name="tanggal_mulai" value="{{ $start }}"></label>
            <label class="form-group"><span class="form-label">Tanggal Selesai</span><input class="form-control" type="date" name="tanggal_selesai" value="{{ $end }}"></label>
            <div class="report-filter__actions"><button class="btn-search"><i class="bi bi-funnel"></i>Tampilkan</button><a class="btn-back" href="{{ route('akuntan.jurnal.index') }}">Reset</a></div>
        </form>
    </section>

    <section class="journal-list">
        @forelse($journals as $journal)
            <article class="journal-entry">
                <header class="journal-entry__head"><div><b>{{ $journal->nomor_jurnal }}</b><span>{{ optional($journal->tanggal_jurnal)->format('d M Y') }} · {{ $journal->transaksi?->berkas?->nasabah?->nama_nasabah ?? '-' }}</span></div><small>Diposting oleh {{ $journal->user?->name ?? '-' }}</small></header>
                <p class="journal-entry__note">{{ $journal->keterangan ?: '-' }}</p>
                <div class="table-responsive"><table class="table journal-table"><thead><tr><th>Akun</th><th>Keterangan</th><th>Debit</th><th>Kredit</th></tr></thead><tbody>@foreach($journal->details as $detail)<tr><td><b>{{ $detail->akun?->kode_akun }}</b> · {{ $detail->akun?->nama_akun }}</td><td>{{ $detail->keterangan }}</td><td>Rp {{ number_format($detail->debit, 0, ',', '.') }}</td><td>Rp {{ number_format($detail->kredit, 0, ',', '.') }}</td></tr>@endforeach</tbody></table></div>
            </article>
        @empty
            <section class="empty-state"><i class="bi bi-journal-x"></i><b>Belum ada jurnal umum</b><span>Jurnal akan tampil setelah Akuntan memposting transaksi.</span></section>
        @endforelse
    </section>
    <div class="mt-4">{{ $journals->links() }}</div>
</div>
@endsection
