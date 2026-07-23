@extends('layouts.cs')

@section('title','Update Tracking Status')

@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-pencil-square"></i> Edit Tracking Status
        </div>
        <div class="page-subtitle">Perbarui histori proses berkas</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">

                {{-- 🔥 FIX DI SINI --}}
                <form action="{{ route('cs.tracking.update', $tracking->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- BERKAS --}}
                    <div class="mb-3">
                        <label class="form-label">Berkas</label>
                        <select name="berkas_id" class="form-select" required>
                            @foreach($berkas as $b)
                                <option value="{{ $b->id }}"
                                    {{ $tracking->berkas_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->jenis_layanan ?? 'Berkas #' . $b->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PETUGAS --}}
                    <div class="mb-3">
                        <label class="form-label">Petugas (CS)</label>
                        <select name="user_id" class="form-select" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ $tracking->user_id == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <input type="text" name="status" class="form-control"
                               value="{{ $tracking->status }}" required>
                    </div>

                    {{-- TANGGAL UPDATE --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal Update</label>
                        <input type="datetime-local" name="tanggal_update"
                               class="form-control"
                               value="{{ optional($tracking->tanggal_update)->format('Y-m-d\TH:i') }}"
                               required>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                                  placeholder="Contoh: Berkas sedang diverifikasi...">{{ $tracking->keterangan }}</textarea>
                    </div>

                    <div class="d-flex gap-3">
                        <button class="btn-submit">
                            <i class="bi bi-save-fill"></i> Update Tracking
                        </button>
                        <a href="{{ route('cs.tracking.index') }}" class="btn-cancel">
                            <i class="bi bi-x-circle-fill"></i> Batal
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection
