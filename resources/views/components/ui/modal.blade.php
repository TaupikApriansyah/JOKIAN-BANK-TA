@props(['id', 'title', 'icon' => 'bi-pencil-square', 'size' => 'md'])

<section id="{{ $id }}" class="app-modal" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" data-modal>
    <button type="button" class="app-modal__backdrop" aria-label="Tutup pop-up" data-modal-close></button>
    <div class="app-modal__dialog {{ $size === 'lg' ? 'app-modal__dialog--lg' : '' }}">
        <header class="app-modal__head">
            <h2 id="{{ $id }}-title" class="app-modal__title"><i class="bi {{ $icon }}"></i>{{ $title }}</h2>
            <button type="button" class="app-modal__close" aria-label="Tutup pop-up" data-modal-close><i class="bi bi-x-lg"></i></button>
        </header>
        <div class="app-modal__body">{{ $slot }}</div>
    </div>
</section>
