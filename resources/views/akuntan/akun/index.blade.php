@extends('layouts.akuntan')
@section('title', 'Daftar Akun')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-journal-bookmark"></i>Daftar Akun</h1>
            <p class="page-subtitle">Master akun sederhana untuk jurnal transaksi layanan. Akun yang sudah ada tetap dapat dipakai sebagai pilihan posting.</p>
        </div>
        <button type="button" class="btn-add" data-modal-open="akun-create-modal"><i class="bi bi-plus-circle"></i>Tambah Akun</button>
    </header>

    <x-ui.flash />

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>Kode</th><th>Nama Akun</th><th>Kelompok</th><th>Saldo Normal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($accounts as $account)
                <tr>
                    <td><span class="account-code">{{ $account->kode_akun }}</span></td>
                    <td class="fw-semibold">{{ $account->nama_akun }}</td>
                    <td>{{ $account->kelompok }}</td>
                    <td>{{ $account->saldo_normal }}</td>
                    <td><span class="status-badge status-{{ $account->status }}">{{ ucfirst($account->status) }}</span></td>
                    <td><button type="button" class="btn-action btn-edit" data-modal-open="akun-edit-{{ $account->id }}"><i class="bi bi-pencil"></i>Edit</button></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada daftar akun.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</div>

<x-ui.modal id="akun-create-modal" title="Tambah Akun" icon="bi-plus-circle">
    <form method="POST" action="{{ route('akuntan.akun.store') }}">@csrf
        @include('akuntan.akun.form', ['account' => null])
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Akun</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
</x-ui.modal>

@foreach($accounts as $account)
    <x-ui.modal id="akun-edit-{{ $account->id }}" title="Edit Akun" icon="bi-pencil-square">
        <form method="POST" action="{{ route('akuntan.akun.update', $account->id) }}">@csrf @method('PUT')
            @include('akuntan.akun.form', ['account' => $account])
            <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Akun</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
    </x-ui.modal>
@endforeach
@endsection
