@props([
    'name' => 'file',
    'id' => 'file_upload',
    'label' => 'Upload File',
    'required' => false,
    'accept' => '.pdf,.jpg,.jpeg,.png,.doc,.docx',
    'help' => 'Format: PDF, JPG, PNG, DOC, DOCX (maks. 10 MB)',
    'currentFile' => null,
    'optionalNote' => null,
    'maxSize' => 10485760,
])

@php
    $hasError = $errors->has($name);
    $emptyText = $currentFile ? 'File saat ini: ' . $currentFile : ($required ? 'Belum ada file dipilih' : 'Tidak mengganti file');
@endphp

<div
    class="siberkas-upload {{ $hasError ? 'is-error' : '' }}"
    data-file-upload
    data-current-file="{{ $currentFile ?? '' }}"
    data-empty-text="{{ $emptyText }}"
    data-max-size="{{ $maxSize }}"
    data-server-error="{{ $hasError ? 'true' : 'false' }}"
>
    <div
        class="siberkas-upload__dropzone"
        data-upload-dropzone
        role="button"
        tabindex="0"
        aria-controls="{{ $id }}"
        aria-label="Pilih file untuk {{ strtolower($label) }}"
    >
        <svg class="siberkas-upload__cloud" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <p class="siberkas-upload__heading" data-upload-heading>Tarik file ke sini</p>
        <p class="siberkas-upload__subheading">atau pilih file dari perangkat</p>
        <span class="siberkas-upload__format" data-upload-format>{{ $help }}</span>
    </div>

    <div class="siberkas-upload__footer">
        <label for="{{ $id }}" class="siberkas-upload__browse">
            <svg viewBox="0 0 32 32" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.331 6H8.5v20h15V14.154h-8.169z"></path>
                <path d="M18.153 6h-.009v5.342H23.5v-.002z"></path>
            </svg>
            <span class="siberkas-upload__filename" data-upload-filename>{{ $emptyText }}</span>
        </label>

        <button
            type="button"
            class="siberkas-upload__clear"
            data-upload-clear
            aria-label="Batalkan file yang dipilih"
            title="Batalkan file yang dipilih"
            disabled
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.16565 10.1534C5.07629 8.99181 5.99473 8 7.15975 8H16.8402C18.0053 8 18.9237 8.9918 18.8344 10.1534L18.142 19.1534C18.0619 20.1954 17.193 21 16.1479 21H7.85206C6.80699 21 5.93811 20.1954 5.85795 19.1534L5.16565 10.1534Z" stroke="currentColor" stroke-width="2" />
                <path d="M19.5 5H4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                <path d="M10 3C10 2.44772 10.4477 2 11 2H13C13.5523 2 14 2.44772 14 3V5H10V3Z" stroke="currentColor" stroke-width="2" />
            </svg>
        </button>
    </div>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="file"
        class="sr-only siberkas-upload__input"
        accept="{{ $accept }}"
        {{ $required ? 'required' : '' }}
        data-upload-input
    >

    @if($optionalNote)
        <p class="siberkas-upload__note">{{ $optionalNote }}</p>
    @endif

    <p class="siberkas-upload__feedback {{ $hasError ? '' : 'hidden' }}" data-upload-feedback aria-live="polite">
        {{ $hasError ? $errors->first($name) : '' }}
    </p>
</div>
