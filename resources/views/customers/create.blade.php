@extends('layouts.app', ['pageTitle' => 'Tambah Nasabah'])
@section('content')
<div class="mx-auto max-w-3xl"><div class="mb-6"><h1 class="text-2xl font-extrabold">Tambah Data Nasabah</h1><p class="mt-1 text-sm text-slate-500">Data sensitif seperti NIK dan rekening akan disimpan terenkripsi.</p></div><form method="POST" action="{{ route('customers.store') }}" class="app-card p-6">@csrf @include('customers.partials.form',['customer'=>null])<div class="mt-7 flex justify-end gap-3"><a href="{{ route('customers.index') }}" class="soft-button">Batal</a><button class="primary-pill"><i data-lucide="save" class="h-4 w-4"></i> Simpan Nasabah</button></div></form></div>
@endsection
