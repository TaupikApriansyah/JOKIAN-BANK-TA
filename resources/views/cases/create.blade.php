@extends('layouts.app', ['pageTitle' => 'Input Berkas Baru'])
@section('content')
<div class="mx-auto max-w-3xl"><div class="mb-6"><h1 class="text-2xl font-extrabold">Input Berkas Baru</h1><p class="mt-1 text-sm text-slate-500">Nomor berkas dan batas SLA dibuat otomatis oleh sistem.</p></div><form method="POST" action="{{ route('cases.store') }}" class="app-card p-6">@csrf @include('cases.partials.form',['serviceCase'=>null])<div class="mt-7 flex justify-end gap-3"><a href="{{ route('cases.index') }}" class="soft-button">Batal</a><button class="primary-pill"><i data-lucide="save" class="h-4 w-4"></i> Buat Berkas</button></div></form></div>
@endsection
