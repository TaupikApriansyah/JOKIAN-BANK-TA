@extends('layouts.app')

@section('title', 'SIBERKAS')

@section('content')
<div class="grid min-h-screen place-items-center bg-gradient-to-br from-emerald-950 via-emerald-700 to-teal-500 p-6 text-center text-white">
    <div class="max-w-lg">
        <div class="mx-auto mb-5 grid h-16 w-16 place-items-center rounded-2xl bg-white/15 text-3xl"><i class="bi bi-files"></i></div>
        <h1 class="text-4xl font-black tracking-tight">SIBERKAS</h1>
        <p class="mt-3 text-sm leading-6 text-emerald-50/85">Sistem Informasi Berkas Terintegrasi.</p>
        <a href="{{ route('login') }}" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-emerald-800 shadow-lg hover:bg-emerald-50"><i class="bi bi-box-arrow-in-right"></i> Masuk ke Sistem</a>
    </div>
</div>
@endsection
