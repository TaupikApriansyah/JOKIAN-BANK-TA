@extends('layouts.cs')
@section('title', 'Transaksi Layanan')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-cash-coin"></i>Transaksi Layanan</h1>
            <p class="page-subtitle">CS mencatat transaksi layanan dan mengirimkannya ke Akuntan untuk diverifikasi.</p>
        </div>
        <button class="btn-add" type="button" data-modal-open="transaksi-create-modal"><i class="bi bi-plus-circle"></i>Tambah Transaksi</button>
    </header>

    <x-ui.flash />

    <section class="info-box"><i class="bi bi-wallet2"></i><span>Total nominal pada hasil saat ini: <b>Rp {{ number_format($totalNominal, 0, ',', '.') }}</b></span></section>

    <section class="search-box">
        <form class="search-form" action="{{ route('cs.transaksi.index') }}">
            <div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ request('search') }}" placeholder="Cari transaksi atau nasabah"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>
            <select class="form-select" name="kategori"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('kategori') === $category)>{{ $category }}</option>@endforeach</select>
            <select class="form-select" name="status"><option value="">Semua status</option>@foreach(array_merge($paymentStatuses, ['Diposting']) as $paymentStatus)<option value="{{ $paymentStatus }}" @selected(request('status') === $paymentStatus)>{{ $paymentStatus }}</option>@endforeach</select>
        </form>
    </section>

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>No</th><th>Nasabah / Berkas</th><th>Jenis</th><th>Status</th><th>Nominal</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($transaksis as $trx)
                <tr>
                    <td><span class="row-number">{{ $loop->iteration }}</span></td>
                    <td><b>{{ $trx->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><small class="d-block text-muted">{{ $trx->berkas?->jenis_layanan ?? '-' }} · {{ optional($trx->tanggal_transaksi)->format('d M Y') }}</small></td>
                    <td><span class="transaction-type transaction-type--{{ strtolower($trx->arah_transaksi ?? 'pemasukan') }}">{{ $trx->arah_transaksi ?? 'Pemasukan' }}</span><small class="d-block text-muted">{{ $trx->kategori }} · {{ $trx->jenis_transaksi }}</small></td>
                    <td><span class="status-badge status-{{ \Illuminate\Support\Str::slug($trx->status_transaksi) }}">{{ $trx->status_transaksi }}</span>@if($trx->catatan_verifikasi)<small class="d-block text-muted">{{ \Illuminate\Support\Str::limit($trx->catatan_verifikasi, 36) }}</small>@endif</td>
                    <td class="fw-bold">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                    <td>
                        <div class="action-buttons">
                            @if($trx->status_transaksi !== 'Diposting')
                                <button class="btn-action btn-edit" type="button" data-modal-open="transaksi-edit-{{ $trx->id }}"><i class="bi bi-pencil"></i>Edit</button>
                                <form method="POST" action="{{ route('cs.transaksi.destroy', $trx->id) }}" onsubmit="return confirm('Hapus transaksi ini?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form>
                            @else
                                <span class="text-muted small"><i class="bi bi-lock-fill"></i> Sudah diposting</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi layanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</div>

<x-ui.modal id="transaksi-create-modal" title="Tambah Transaksi Layanan" icon="bi-plus-circle" size="lg">
    <form method="POST" action="{{ route('cs.transaksi.store') }}" enctype="multipart/form-data">@csrf
        <input type="hidden" name="_modal" value="transaksi-create-modal">
        @include('cs.transaksi.form', ['transaction' => null])
        <div class="form-actions"><button class="btn-save"><i class="bi bi-send-check"></i>Simpan &amp; Kirim</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
</x-ui.modal>

@foreach($transaksis->where('status_transaksi', '!=', 'Diposting') as $trx)
    <x-ui.modal id="transaksi-edit-{{ $trx->id }}" title="Edit Transaksi" icon="bi-pencil-square" size="lg">
        <form method="POST" action="{{ route('cs.transaksi.update', $trx->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
            <input type="hidden" name="_modal" value="transaksi-edit-{{ $trx->id }}">
            @include('cs.transaksi.form', ['transaction' => $trx])
            <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Transaksi</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
    </x-ui.modal>
@endforeach
@endsection
