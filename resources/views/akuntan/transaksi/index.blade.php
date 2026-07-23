@extends('layouts.akuntan')
@section('title', 'Verifikasi Transaksi')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-patch-check"></i>Verifikasi Transaksi</h1>
            <p class="page-subtitle">Periksa data transaksi dari CS. Saat diposting, jurnal debit dan kredit dibuat otomatis.</p>
        </div>
    </header>

    <x-ui.flash />

    @if($accounts->count() < 2)
        <section class="warning-box"><i class="bi bi-exclamation-triangle"></i><span>Minimal dua akun aktif diperlukan untuk memposting transaksi. Tambahkan akun dari menu Daftar Akun.</span></section>
    @endif

    <section class="search-box">
        <form class="search-form" method="GET" action="{{ route('akuntan.transaksi.index') }}">
            <div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ $search }}" placeholder="Cari nasabah, kategori, atau transaksi"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>
            <select class="form-select" name="status">@foreach($statuses as $item)<option value="{{ $item }}" @selected($status === $item)>{{ $item === 'Semua' ? 'Semua status' : $item }}</option>@endforeach</select>
            <a class="btn-back" href="{{ route('akuntan.transaksi.index') }}">Reset</a>
        </form>
    </section>

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>No</th><th>Nasabah / Berkas</th><th>Transaksi</th><th>Bukti</th><th>Nominal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td><span class="row-number">{{ $loop->iteration }}</span></td>
                    <td><b>{{ $transaction->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><small class="d-block text-muted">{{ $transaction->berkas?->jenis_layanan ?? '-' }}</small></td>
                    <td><span class="transaction-type transaction-type--{{ strtolower($transaction->arah_transaksi ?? 'pemasukan') }}">{{ $transaction->arah_transaksi }}</span><small class="d-block text-muted">{{ $transaction->kategori }} · {{ $transaction->jenis_transaksi }}</small></td>
                    <td>@if($transaction->bukti_pembayaran)<a class="file-link" href="{{ route('dokumen.bukti.download', $transaction->id) }}"><i class="bi bi-file-earmark-check"></i>Unduh bukti</a>@else<span class="text-muted small">Belum diunggah</span>@endif</td>
                    <td class="fw-bold">Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</td>
                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($transaction->status_transaksi) }}">{{ $transaction->status_transaksi }}</span>@if($transaction->catatan_verifikasi)<small class="d-block text-muted">{{ \Illuminate\Support\Str::limit($transaction->catatan_verifikasi, 42) }}</small>@endif</td>
                    <td>
                        @if(in_array($transaction->status_transaksi, ['Menunggu Verifikasi', 'Lunas']))
                            <div class="action-buttons">
                                <button type="button" class="btn-action btn-save" data-modal-open="post-transaksi-{{ $transaction->id }}"><i class="bi bi-journal-plus"></i>Posting</button>
                                <button type="button" class="btn-action btn-delete" data-modal-open="reject-transaksi-{{ $transaction->id }}"><i class="bi bi-x-circle"></i>Tolak</button>
                            </div>
                        @elseif($transaction->jurnal)
                            <span class="text-muted small"><i class="bi bi-journal-check"></i>{{ $transaction->jurnal->nomor_jurnal }}</span>
                        @else
                            <span class="text-muted small">Tidak ada aksi</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada transaksi pada filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</div>

@foreach($transactions->whereIn('status_transaksi', ['Menunggu Verifikasi', 'Lunas']) as $transaction)
    @php
        $cash = $accounts->firstWhere('kode_akun', '111');
        $adminIncome = $accounts->firstWhere('kode_akun', '411');
        $serviceIncome = $accounts->firstWhere('kode_akun', '412');
        $atkExpense = $accounts->firstWhere('kode_akun', '512');
        $transportExpense = $accounts->firstWhere('kode_akun', '513');
        $operationalExpense = $accounts->firstWhere('kode_akun', '511');
        $isIncome = $transaction->arah_transaksi === 'Pemasukan';
        $defaultDebit = $isIncome ? optional($cash)->id : optional($transaction->kategori === 'ATK dan Cetak' ? $atkExpense : ($transaction->kategori === 'Transportasi' ? $transportExpense : $operationalExpense))->id;
        $defaultCredit = $isIncome ? optional($transaction->kategori === 'Biaya Layanan' ? $serviceIncome : $adminIncome)->id : optional($cash)->id;
    @endphp
    <x-ui.modal id="post-transaksi-{{ $transaction->id }}" title="Posting Jurnal" icon="bi-journal-plus" size="lg">
        <div class="posting-preview">
            <span><b>{{ $transaction->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><small>{{ $transaction->jenis_transaksi }} · {{ $transaction->kategori }}</small></span>
            <strong>Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</strong>
        </div>
        <form method="POST" action="{{ route('akuntan.transaksi.post', $transaction->id) }}">@csrf
            <div class="modal-form-grid">
                <label class="form-group"><span class="form-label">Akun Debit</span><select class="form-select" name="akun_debit_id" required><option value="">Pilih akun debit</option>@foreach($accounts as $account)<option value="{{ $account->id }}" @selected($defaultDebit == $account->id)>{{ $account->kode_akun }} · {{ $account->nama_akun }}</option>@endforeach</select></label>
                <label class="form-group"><span class="form-label">Akun Kredit</span><select class="form-select" name="akun_kredit_id" required><option value="">Pilih akun kredit</option>@foreach($accounts as $account)<option value="{{ $account->id }}" @selected($defaultCredit == $account->id)>{{ $account->kode_akun }} · {{ $account->nama_akun }}</option>@endforeach</select></label>
                <label class="form-group span-2"><span class="form-label">Catatan Verifikasi</span><textarea class="form-control" name="catatan_verifikasi" rows="3" placeholder="Opsional. Contoh: bukti pembayaran sesuai."></textarea></label>
            </div>
            <div class="mini-note"><i class="bi bi-info-circle"></i>Sistem membuat satu jurnal umum dengan dua detail: debit dan kredit.</div>
            <div class="form-actions"><button class="btn-save" {{ $accounts->count() < 2 ? 'disabled' : '' }}><i class="bi bi-check2-circle"></i>Verifikasi &amp; Posting</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
    </x-ui.modal>

    <x-ui.modal id="reject-transaksi-{{ $transaction->id }}" title="Tolak Transaksi" icon="bi-x-circle">
        <form method="POST" action="{{ route('akuntan.transaksi.reject', $transaction->id) }}">@csrf
            <label class="form-group"><span class="form-label">Alasan Penolakan</span><textarea class="form-control" name="catatan_verifikasi" rows="4" placeholder="Jelaskan data yang perlu diperbaiki CS." required></textarea></label>
            <div class="form-actions"><button class="btn-delete"><i class="bi bi-send-x"></i>Tolak &amp; Kembalikan</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
    </x-ui.modal>
@endforeach
@endsection
