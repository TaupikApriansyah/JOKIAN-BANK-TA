@php($count = $pendingVerificationCount ?? 0)
<div class="app-notice" data-notice>
    <button type="button" class="app-icon-button" aria-label="Transaksi menunggu verifikasi" data-notice-toggle>
        <i class="bi bi-bell"></i>@if($count)<span class="app-notice__count">{{ $count > 9 ? '9+' : $count }}</span>@endif
    </button>
    <div class="app-notice__panel">
        <div class="app-notice__head">
            <span>Menunggu verifikasi {{ $count ? '(' . $count . ')' : '' }}</span>
            <button type="button" class="app-notice__close" aria-label="Tutup notifikasi" data-notice-close><i class="bi bi-x-lg"></i></button>
        </div>
        @forelse(($pendingVerificationTransactions ?? collect()) as $transaction)
            <div class="app-notice__item">
                <b>{{ $transaction->berkas?->nasabah?->nama_nasabah ?? 'Nasabah' }}</b>
                <span>{{ $transaction->kategori }} · Rp {{ number_format($transaction->nominal, 0, ',', '.') }}</span>
            </div>
        @empty
            <div class="app-notice__item"><span>Tidak ada transaksi yang menunggu verifikasi.</span></div>
        @endforelse
    </div>
</div>
