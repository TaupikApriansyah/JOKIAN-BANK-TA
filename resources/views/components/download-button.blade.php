@props(['href', 'label' => 'Unduh Excel'])
<a href="{{ $href }}" class="bank-download-button" title="{{ $label }}">
    <span class="button__text">{{ $label }}</span>
    <span class="button__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14"></path></svg></span>
</a>
