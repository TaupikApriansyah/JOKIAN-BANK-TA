@extends('layouts.app', ['pageTitle' => 'Input Berkas Baru'])
@section('content')
<div class="mx-auto max-w-3xl">
  <div class="mb-6"><h1 class="text-2xl font-extrabold">Input Berkas Baru</h1><p class="mt-1 text-sm text-slate-500">Pilih nasabah dan layanan. Nomor berkas serta batas SLA dibuat otomatis oleh sistem.</p></div>

  @if($customers->isEmpty())
    <section class="app-card p-6 text-center">
      <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-blue-50 text-brand-700"><i data-lucide="user-round-plus" class="h-6 w-6"></i></div>
      <h2 class="mt-4 text-lg font-extrabold">Belum ada nasabah aktif yang dapat dipilih.</h2>
      <p class="mt-2 text-sm text-slate-500">Buat data nasabah terlebih dahulu. Setelah tersimpan, nasabah akan otomatis tersedia pada formulir berkas.</p>
      <a href="{{ route('customers.create') }}" class="primary-pill mt-5"><i data-lucide="user-plus" class="h-4 w-4"></i> Tambah Nasabah</a>
    </section>
  @elseif($serviceTypes->isEmpty())
    <section class="app-card p-6 text-center">
      <div class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-amber-50 text-amber-700"><i data-lucide="list-x" class="h-6 w-6"></i></div>
      <h2 class="mt-4 text-lg font-extrabold">Master jenis layanan belum tersedia.</h2>
      <p class="mt-2 text-sm text-slate-500">Minta Admin menambahkan jenis layanan dan aturan SLA sebelum berkas baru dibuat.</p>
      <a href="{{ route('cases.index') }}" class="soft-button mt-5">Kembali ke Berkas</a>
    </section>
  @else
    <form method="POST" action="{{ route('cases.store') }}" class="app-card p-6" data-processing-overlay>
      @csrf
      @include('cases.partials.form',['serviceCase'=>null, 'selectedCustomerId'=>$selectedCustomerId])
      <div class="mt-7 flex justify-end gap-3"><a href="{{ route('cases.index') }}" class="soft-button">Batal</a><button class="primary-pill"><i data-lucide="save" class="h-4 w-4"></i> Buat Berkas</button></div>
    </form>
  @endif
</div>
@endsection
