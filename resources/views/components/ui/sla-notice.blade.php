@php($count = $slaAlertCount ?? 0)
<div class="app-notice" data-notice>
    <button type="button" class="app-icon-button" aria-label="Notifikasi SLA" data-notice-toggle>
        <i class="bi bi-bell"></i>@if($count)<span class="app-notice__count">{{ $count > 9 ? '9+' : $count }}</span>@endif
    </button>
    <div class="app-notice__panel">
        <div class="app-notice__head">
            <span>Notifikasi SLA {{ $count ? '(' . $count . ')' : '' }}</span>
            <button type="button" class="app-notice__close" aria-label="Tutup notifikasi" data-notice-close><i class="bi bi-x-lg"></i></button>
        </div>
        @forelse(($slaAlerts ?? collect()) as $alert)
            <div class="app-notice__item">
                <b>{{ $alert->nasabah?->nama_nasabah ?? 'Berkas nasabah' }}</b>
                <span>{{ $alert->jenis_layanan }} · {{ $alert->sla_label }}</span>
            </div>
        @empty
            <div class="app-notice__item"><span>Tidak ada peringatan SLA saat ini.</span></div>
        @endforelse
    </div>
</div>
