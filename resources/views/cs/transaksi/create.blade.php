@extends('layouts.cs')
@section('title', 'Tambah Transaksi')

@section('content')
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-cash-coin"></i>Tambah Transaksi</h1>
            <p class="page-subtitle">Catat transaksi layanan dan kirim ke Akuntan untuk diverifikasi.</p>
        </div>
        <a class="btn-back" href="{{ route('cs.transaksi.index') }}"><i class="bi bi-arrow-left"></i>Kembali</a>
    </header>

    <x-ui.flash />

    <section class="form-card">
        <form method="POST" action="{{ route('cs.transaksi.store') }}" enctype="multipart/form-data">
            @csrf
            @include('cs.transaksi.form', ['transaction' => null])
            <div class="form-actions"><button class="btn-save"><i class="bi bi-send-check"></i>Simpan Transaksi</button><a class="btn-back" href="{{ route('cs.transaksi.index') }}">Batal</a></div>
        </form>
    </section>
</div>
@endsection
