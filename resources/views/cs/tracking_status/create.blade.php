@extends('layouts.cs')
@section('title','Tambah Tracking Status')
@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-plus-circle-fill"></i>
            Tambah Tracking Status
        </div>
        <div class="page-subtitle">Isi status terbaru dari berkas</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">

                {{-- ERROR VALIDATION --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('cs.tracking.store') }}">
                    @csrf

                    {{-- BERKAS --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-folder-fill"></i> Berkas
                            <span class="required">*</span>
                        </label>

                        <select name="berkas_id" class="form-control" required>
                            <option value="">-- Pilih Berkas --</option>
                            @foreach($berkas as $b)
    <option value="{{ $b->id }}"
        {{ old('berkas_id') == $b->id ? 'selected' : '' }}>
        {{ $b->jenis_layanan }}
    </option>
@endforeach

                        </select>
                    </div>

                    {{-- CS --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-badge-fill"></i> CS
                            <span class="required">*</span>
                        </label>

                        <select name="user_id" class="form-control" required>
                            <option value="">-- Pilih CS --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-flag-fill"></i> Status
                            <span class="required">*</span>
                        </label>

                        <input type="text"
                               name="status"
                               class="form-control"
                               value="{{ old('status') }}"
                               placeholder="Contoh: Diproses / Diterima / Ditolak"
                               required>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar-event-fill"></i> Tanggal Update
                            <span class="required">*</span>
                        </label>

                        <input type="datetime-local"
                               name="tanggal_update"
                               class="form-control"
                               value="{{ old('tanggal_update') }}"
                               required>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-chat-left-text-fill"></i> Keterangan
                        </label>

                        <textarea name="keterangan"
                                  class="form-control"
                                  placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    </div>

                    {{-- ACTION --}}
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle-fill"></i> Simpan
                        </button>
                        <a href="{{ route('cs.tracking.index') }}" class="btn-cancel">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

@endsection
