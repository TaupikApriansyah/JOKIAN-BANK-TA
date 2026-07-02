@extends('layouts.app', ['pageTitle' => 'Ubah Nasabah'])
@section('content')
<div class="mx-auto max-w-3xl"><div class="mb-6"><p class="font-mono text-xs font-bold text-brand-700">{{ $customer->customer_number }}</p><h1 class="text-2xl font-extrabold">Ubah Data Nasabah</h1><p class="mt-1 text-sm text-slate-500">Perubahan dicatat otomatis pada audit trail.</p></div><form method="POST" action="{{ route('customers.update',$customer) }}" class="app-card p-6">@csrf @method('PUT') @include('customers.partials.form',['customer'=>$customer])<div class="mt-7 flex justify-end gap-3"><a href="{{ route('customers.show',$customer) }}" class="soft-button">Batal</a><button class="primary-pill"><i data-lucide="save" class="h-4 w-4"></i> Simpan Perubahan</button></div></form></div>
@endsection
