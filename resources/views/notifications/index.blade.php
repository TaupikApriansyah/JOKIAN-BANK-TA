@extends('layouts.app', ['pageTitle' => 'Notifikasi SLA'])

@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold">Notifikasi SLA</h1><p class="mt-1 text-sm text-slate-500">Notifikasi dibuat otomatis saat SLA mendekati batas atau terlewati.</p></div>
<div class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="divide-y">
        @forelse($notifications as $notification)
            <div class="flex flex-col gap-3 p-5 md:flex-row md:items-center md:justify-between {{ $notification->read_at ? 'bg-white' : 'bg-yellow-50/50' }}">
                <div><p class="font-semibold">{{ $notification->title }}</p><p class="mt-1 text-sm text-slate-600">{{ $notification->message }}</p><p class="mt-1 text-xs text-slate-400">{{ $notification->created_at->format('d M Y H:i') }}</p></div>
                <form method="POST" action="{{ route('notifications.read', $notification) }}">@csrf<button class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold">{{ $notification->read_at ? 'Buka Berkas' : 'Tandai Dibaca & Buka' }}</button></form>
            </div>
        @empty
            <div class="p-10 text-center text-sm text-slate-500">Belum ada notifikasi SLA untuk Anda.</div>
        @endforelse
    </div>
</div>
<div class="mt-5">{{ $notifications->links() }}</div>
@endsection
