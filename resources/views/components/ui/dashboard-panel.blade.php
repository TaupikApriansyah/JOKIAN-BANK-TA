@props(['title', 'icon' => 'bi-grid', 'link' => null, 'linkText' => 'Lihat semua'])
<section class="dashboard-panel">
    <header class="dashboard-panel__head">
        <h2 class="dashboard-panel__title"><i class="bi {{ $icon }}"></i>{{ $title }}</h2>
        @if($link)<a class="dashboard-panel__link" href="{{ $link }}">{{ $linkText }} <i class="bi bi-arrow-right"></i></a>@endif
    </header>
    <div class="dashboard-panel__body">{{ $slot }}</div>
</section>
