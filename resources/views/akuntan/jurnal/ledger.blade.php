@extends('layouts.akuntan')
@section('title', 'Buku Besar')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-table"></i>Buku Besar</h1><p class="page-subtitle">Mutasi jurnal berdasarkan akun, dengan saldo berjalan sederhana.</p></div></header>

    <section class="report-filter">
        <form class="report-filter__form" method="GET" action="{{ route('akuntan.jurnal.ledger') }}">
            <label class="form-group"><span class="form-label">Akun</span><select class="form-select" name="akun_id">@foreach($accounts as $item)<option value="{{ $item->id }}" @selected($account && $account->id == $item->id)>{{ $item->kode_akun }} · {{ $item->nama_akun }}</option>@endforeach</select></label>
            <label class="form-group"><span class="form-label">Tanggal Mulai</span><input class="form-control" type="date" name="tanggal_mulai" value="{{ $start }}"></label>
            <label class="form-group"><span class="form-label">Tanggal Selesai</span><input class="form-control" type="date" name="tanggal_selesai" value="{{ $end }}"></label>
            <div class="report-filter__actions"><button class="btn-search"><i class="bi bi-funnel"></i>Tampilkan</button></div>
        </form>
    </section>

    @if($account)
        <section class="ledger-head"><div><span>Akun aktif</span><h2>{{ $account->kode_akun }} · {{ $account->nama_akun }}</h2><p>Saldo normal: {{ $account->saldo_normal }}</p></div><b>Saldo Akhir<br><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></b></section>
        <section class="table-wrapper"><table class="table table-hover"><thead><tr><th>Tanggal</th><th>Jurnal</th><th>Keterangan</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ optional($row->jurnal?->tanggal_jurnal)->format('d M Y') }}</td><td>{{ $row->jurnal?->nomor_jurnal }}</td><td>{{ $row->jurnal?->keterangan }}</td><td>Rp {{ number_format($row->debit, 0, ',', '.') }}</td><td>Rp {{ number_format($row->kredit, 0, ',', '.') }}</td><td class="fw-bold">Rp {{ number_format($row->saldo_berjalan, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada mutasi jurnal untuk akun ini.</td></tr>@endforelse</tbody></table></section>
    @else
        <section class="empty-state"><i class="bi bi-journal-x"></i><b>Belum ada akun aktif</b><span>Tambahkan akun terlebih dahulu di menu Daftar Akun.</span></section>
    @endif
</div>
@endsection
