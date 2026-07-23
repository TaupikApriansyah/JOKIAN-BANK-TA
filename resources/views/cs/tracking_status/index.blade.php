@extends('layouts.cs')
@section('title', 'Tracking Berkas')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-activity"></i>Tracking Berkas</h1><p class="page-subtitle">Catat perubahan status agar riwayat berkas tetap rapi.</p></div><button class="btn-add" type="button" data-modal-open="tracking-create-modal"><i class="bi bi-plus-circle"></i>Tambah Tracking</button></header>
    <x-ui.flash />
    <section class="search-box"><form class="search-form" action="{{ route('cs.tracking.index') }}"><div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ request('search') }}" placeholder="Cari status, keterangan, atau nasabah"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>@if(request('search'))<a class="btn-reset" href="{{ route('cs.tracking.index') }}">Reset</a>@endif</form></section>
    <section class="table-wrapper"><table class="table table-hover"><thead><tr><th>No</th><th>Nasabah</th><th>Berkas</th><th>Status</th><th>Waktu Update</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>
    @forelse($trackings as $tracking)
        <tr><td><span class="row-number">{{ $loop->iteration }}</span></td><td class="fw-semibold">{{ $tracking->berkas?->nasabah?->nama_nasabah ?? '-' }}</td><td>{{ $tracking->berkas?->jenis_layanan ?? '-' }}</td><td><span class="status-badge status-{{ strtolower($tracking->status) }}">{{ $tracking->status }}</span></td><td>{{ \Carbon\Carbon::parse($tracking->tanggal_update)->format('d M Y H:i') }}</td><td>{{ \Illuminate\Support\Str::limit($tracking->keterangan, 45) }}</td><td><div class="action-buttons"><a class="btn-action btn-info" href="{{ route('cs.tracking.show', $tracking->id) }}"><i class="bi bi-eye"></i>Detail</a><button class="btn-action btn-edit" type="button" data-modal-open="tracking-edit-{{ $tracking->id }}"><i class="bi bi-pencil"></i>Edit</button><form method="POST" action="{{ route('cs.tracking.destroy', $tracking->id) }}" onsubmit="return confirm('Hapus riwayat tracking ini?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td></tr>
    @empty<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat tracking.</td></tr>@endforelse
    </tbody></table></section>
</div>

<x-ui.modal id="tracking-create-modal" title="Tambah Tracking" icon="bi-plus-circle" size="lg">
<form method="POST" action="{{ route('cs.tracking.store') }}">@csrf
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Berkas</span><select class="form-select" name="berkas_id" required><option value="">Pilih berkas</option>@foreach($berkasList as $berkas)<option value="{{ $berkas->id }}">{{ $berkas->nasabah?->nama_nasabah }} · {{ $berkas->jenis_layanan }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Status</span><select class="form-select" name="status" required><option value="Diterima">Diterima</option><option value="Diproses">Diproses</option><option value="Selesai">Selesai</option></select></label><label class="form-group"><span class="form-label">Tanggal & Jam</span><input class="form-control" type="datetime-local" name="tanggal_update" value="{{ now()->format('Y-m-d\TH:i') }}" required></label><label class="form-group span-2"><span class="form-label">Keterangan</span><textarea class="form-control" name="keterangan" rows="3" placeholder="Contoh: Dokumen telah diverifikasi"></textarea></label></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Tracking</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>

@foreach($trackings as $tracking)
<x-ui.modal id="tracking-edit-{{ $tracking->id }}" title="Edit Tracking" icon="bi-pencil-square" size="lg">
<form method="POST" action="{{ route('cs.tracking.update', $tracking->id) }}">@csrf @method('PUT')
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Berkas</span><select class="form-select" name="berkas_id" required>@foreach($berkasList as $berkas)<option value="{{ $berkas->id }}" @selected($tracking->berkas_id === $berkas->id)>{{ $berkas->nasabah?->nama_nasabah }} · {{ $berkas->jenis_layanan }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Status</span><select class="form-select" name="status" required>@foreach(['Diterima','Diproses','Selesai'] as $status)<option value="{{ $status }}" @selected($tracking->status === $status)>{{ $status }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Tanggal & Jam</span><input class="form-control" type="datetime-local" name="tanggal_update" value="{{ \Carbon\Carbon::parse($tracking->tanggal_update)->format('Y-m-d\TH:i') }}" required></label><label class="form-group span-2"><span class="form-label">Keterangan</span><textarea class="form-control" name="keterangan" rows="3">{{ $tracking->keterangan }}</textarea></label></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Tracking</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>
@endforeach
@endsection
