@extends('layouts.app', ['pageTitle' => 'Halaman Tidak Ditemukan'])
@section('content')
<div class="mx-auto max-w-xl py-10 text-center">
  <div class="app-card p-8">
    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-50 text-brand-700"><i data-lucide="map-pin-off" class="h-7 w-7"></i></div>
    <p class="mt-5 text-sm font-bold text-brand-700">404 · Link tidak tersedia</p>
    <h2 class="mt-2 text-2xl font-extrabold text-slate-900">Halaman yang dicari tidak ditemukan.</h2>
    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">Link mungkin sudah berubah, data sudah tidak tersedia, atau Anda tidak memiliki akses pada modul tersebut.</p>
    <div class="mt-6 flex flex-wrap justify-center gap-3">
      <a href="{{ route('dashboard') }}" class="primary-pill"><i data-lucide="layout-dashboard" class="h-4 w-4"></i> Dashboard</a>
      <a href="{{ route('customers.index') }}" class="soft-button"><i data-lucide="users-round" class="h-4 w-4"></i> Daftar Nasabah</a>
      <a href="{{ route('cases.index') }}" class="soft-button"><i data-lucide="folder-open" class="h-4 w-4"></i> Berkas Layanan</a>
    </div>
  </div>
</div>
@endsection
