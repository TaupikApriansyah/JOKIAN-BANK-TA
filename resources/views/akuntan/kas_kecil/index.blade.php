@extends('layouts.akuntan')
@section('title', 'Petty Cash')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-wallet2"></i>Petty Cash / Kas Kecil</h1>
            <p class="page-subtitle">Catat dana masuk, pengeluaran kecil, bukti transaksi, dan saldo kas kecil.</p>
        </div>
        <button class="btn-add" type="button" data-modal-open="kas-kecil-create-modal"><i class="bi bi-plus-circle"></i>Tambah Transaksi</button>
    </header>

    <x-ui.flash />

    <section class="metric-grid">
        <article class="metric">
            <div class="metric__top"><span class="metric__label">Saldo Kas Kecil</span><span class="metric__icon"><i class="bi bi-wallet2"></i></span></div>
            <b class="metric__value metric__money">Rp {{ number_format($saldo, 0, ',', '.') }}</b>
            <p class="metric__text">Saldo tersedia saat ini</p>
        </article>
        <article class="metric">
            <div class="metric__top"><span class="metric__label">Total Dana Masuk</span><span class="metric__icon"><i class="bi bi-arrow-down-circle"></i></span></div>
            <b class="metric__value metric__money">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</b>
            <p class="metric__text">Akumulasi seluruh pengisian</p>
        </article>
        <article class="metric metric--rose">
            <div class="metric__top"><span class="metric__label">Total Dana Keluar</span><span class="metric__icon"><i class="bi bi-arrow-up-circle"></i></span></div>
            <b class="metric__value metric__money">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</b>
            <p class="metric__text">Akumulasi seluruh pengeluaran</p>
        </article>
        <article class="metric metric--warn">
            <div class="metric__top"><span class="metric__label">Hasil Filter</span><span class="metric__icon"><i class="bi bi-funnel"></i></span></div>
            <b class="metric__value">{{ $transactions->count() }}</b>
            <p class="metric__text">Masuk Rp {{ number_format($filteredMasuk, 0, ',', '.') }} · Keluar Rp {{ number_format($filteredKeluar, 0, ',', '.') }}</p>
        </article>
    </section>

    <section class="search-box">
        <form class="search-form" method="GET" action="{{ route('akuntan.kas-kecil.index') }}">
            <div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ $search }}" placeholder="Cari kategori, keterangan, atau nomor bukti"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>
            <select class="form-select" name="jenis"><option value="">Semua jenis</option><option value="Masuk" @selected($jenis === 'Masuk')>Dana Masuk</option><option value="Keluar" @selected($jenis === 'Keluar')>Dana Keluar</option></select>
            <input class="form-control" type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" aria-label="Tanggal awal">
            <input class="form-control" type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" aria-label="Tanggal akhir">
            <button class="btn-search"><i class="bi bi-funnel"></i>Filter</button>
            <a class="btn-back" href="{{ route('akuntan.kas-kecil.index') }}">Reset</a>
        </form>
    </section>

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>No</th><th>Tanggal</th><th>Jenis</th><th>Kategori / Keterangan</th><th>Nomor Bukti</th><th>Nominal</th><th>Petugas</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td><span class="row-number">{{ $loop->iteration }}</span></td>
                    <td>{{ optional($transaction->tanggal)->format('d M Y') }}</td>
                    <td><span class="transaction-type transaction-type--{{ $transaction->jenis === 'Masuk' ? 'pemasukan' : 'pengeluaran' }}">{{ $transaction->jenis === 'Masuk' ? 'Dana Masuk' : 'Dana Keluar' }}</span></td>
                    <td><b>{{ $transaction->kategori }}</b><small class="d-block text-muted">{{ $transaction->keterangan }}</small></td>
                    <td>{{ $transaction->nomor_bukti ?: '-' }}</td>
                    <td class="fw-bold">{{ $transaction->nominal_rupiah }}</td>
                    <td>{{ $transaction->pembuat?->name ?? '-' }}</td>
                    <td><div class="action-buttons"><button class="btn-action btn-edit" type="button" data-modal-open="kas-kecil-edit-{{ $transaction->id }}"><i class="bi bi-pencil"></i>Edit</button><form method="POST" action="{{ route('akuntan.kas-kecil.destroy', $transaction->id) }}" onsubmit="return confirm('Hapus transaksi kas kecil ini?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada transaksi kas kecil.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</div>

<x-ui.modal id="kas-kecil-create-modal" title="Tambah Transaksi Kas Kecil" icon="bi-wallet2" size="lg">
    <form method="POST" action="{{ route('akuntan.kas-kecil.store') }}">@csrf
        <input type="hidden" name="_modal" value="kas-kecil-create-modal">
        @include('akuntan.kas_kecil.partials.form', ['transaction' => null])
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Transaksi</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
</x-ui.modal>

@foreach($transactions as $transaction)
<x-ui.modal id="kas-kecil-edit-{{ $transaction->id }}" title="Edit Transaksi Kas Kecil" icon="bi-pencil-square" size="lg">
    <form method="POST" action="{{ route('akuntan.kas-kecil.update', $transaction->id) }}">@csrf @method('PUT')
        <input type="hidden" name="_modal" value="kas-kecil-edit-{{ $transaction->id }}">
        @include('akuntan.kas_kecil.partials.form', ['transaction' => $transaction])
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Transaksi</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
</x-ui.modal>
@endforeach
@endsection
