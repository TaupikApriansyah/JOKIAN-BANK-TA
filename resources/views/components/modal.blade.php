@props([
    'id',
    'title',
    'subtitle' => null,
    'size' => 'lg',
])
@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-md',
        'md' => 'max-w-2xl',
        'xl' => 'max-w-5xl',
        default => 'max-w-3xl',
    };
@endphp
<div id="{{ $id }}" class="bank-modal" data-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <button type="button" class="bank-modal-backdrop" data-modal-close aria-label="Tutup pop-up"></button>
    <div class="bank-modal-panel {{ $sizeClass }}" role="document">
        <div class="bank-modal-header">
            <div class="min-w-0">
                <h2 id="{{ $id }}-title" class="text-lg font-extrabold text-slate-900">{{ $title }}</h2>
                @if($subtitle)<p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>@endif
            </div>
            <button type="button" class="bank-modal-close" data-modal-close aria-label="Tutup"><i data-lucide="x" class="h-5 w-5"></i></button>
        </div>
        <div class="bank-modal-body">
            @if(old('_popup') === $id && $errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-extrabold">Data belum dapat disimpan.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
