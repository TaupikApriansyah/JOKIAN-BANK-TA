@extends('layouts.cs')
@section('title', 'Data Berkas')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-folder2-open"></i>Data Berkas</h1><p class="page-subtitle">Berkas layanan, status proses, dan batas SLA.</p></div><button type="button" class="btn-add" data-modal-open="berkas-create-modal"><i class="bi bi-folder-plus"></i>Tambah Berkas</button></header>
    <x-ui.flash />
    <section class="search-box"><form class="search-form" action="{{ route('cs.berkas.index') }}"><div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ request('search') }}" placeholder="Cari nama nasabah atau jenis layanan"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>@if(request('search'))<a class="btn-reset" href="{{ route('cs.berkas.index') }}">Reset</a>@endif</form></section>
    <section class="table-wrapper"><table class="table table-hover"><thead><tr><th>No</th><th>Nasabah</th><th>Layanan</th><th>Masuk</th><th>SLA</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
    @forelse($berkas as $item)
        @php($days = $item->estimasi_selesai ? now()->startOfDay()->diffInDays($item->estimasi_selesai, false) : null)
        <tr><td><span class="row-number">{{ $loop->iteration }}</span></td><td class="fw-semibold">{{ $item->nasabah?->nama_nasabah ?? '-' }}</td><td>{{ $item->jenis_layanan }}</td><td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}</td><td>@if($item->estimasi_selesai)<span class="status-badge {{ $days < 0 ? 'status-ditolak' : ($days <= 2 ? 'status-menunggu' : 'status-diproses') }}">{{ \Carbon\Carbon::parse($item->estimasi_selesai)->format('d M Y') }}@if($days <= 2) · {{ $days < 0 ? 'terlewat' : $days . ' hari' }}@endif</span>@else-@endif</td><td><span class="status-badge status-{{ strtolower($item->status_berkas) }}">{{ $item->status_berkas }}</span></td><td><div class="action-buttons"><a class="btn-action btn-info" href="{{ route('cs.berkas.show', $item->id) }}"><i class="bi bi-eye"></i>Detail</a><button type="button" class="btn-action btn-edit" data-modal-open="berkas-edit-{{ $item->id }}"><i class="bi bi-pencil"></i>Edit</button><button type="button" class="btn-action btn-warning" data-modal-open="berkas-status-{{ $item->id }}"><i class="bi bi-arrow-repeat"></i>Status</button><form method="POST" action="{{ route('cs.berkas.destroy', $item->id) }}" onsubmit="return confirm('Hapus berkas ini beserta data terkait?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td></tr>
    @empty<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data berkas.</td></tr>@endforelse
    </tbody></table></section>
</div>

<x-ui.modal id="berkas-create-modal" title="Tambah Berkas" icon="bi-folder-plus" size="lg">
<form method="POST" action="{{ route('cs.berkas.store') }}">@csrf
    <input type="hidden" name="_modal" value="berkas-create-modal">
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Nasabah</span><select class="form-select" name="id_nasabah" required><option value="">Pilih nasabah</option>@foreach($nasabah as $item)<option value="{{ $item->id }}" @selected(old('id_nasabah') == $item->id)>{{ $item->nama_nasabah }} · {{ $item->nik }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Jenis Layanan</span><input class="form-control" name="jenis_layanan" value="{{ old('jenis_layanan') }}" placeholder="Contoh: Pembukaan Rekening" required></label><label class="form-group"><span class="form-label">Tanggal Masuk</span><input class="form-control" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required></label><label class="form-group"><span class="form-label">Estimasi Selesai</span><input class="form-control" type="date" name="estimasi_selesai" value="{{ old('estimasi_selesai') }}"></label></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Berkas</button><button class="btn-back" type="button" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>

@foreach($berkas as $item)
<x-ui.modal id="berkas-edit-{{ $item->id }}" title="Edit Berkas" icon="bi-pencil-square" size="lg">
<form method="POST" action="{{ route('cs.berkas.update', $item->id) }}">@csrf @method('PUT')
    <input type="hidden" name="_modal" value="berkas-edit-{{ $item->id }}">
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Nasabah</span><select class="form-select" name="id_nasabah" required>@foreach($nasabah as $nas)<option value="{{ $nas->id }}" @selected($item->id_nasabah === $nas->id)>{{ $nas->nama_nasabah }} · {{ $nas->nik }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Jenis Layanan</span><input class="form-control" name="jenis_layanan" value="{{ $item->jenis_layanan }}" required></label><label class="form-group"><span class="form-label">Tanggal Masuk</span><input class="form-control" type="date" name="tanggal_masuk" value="{{ optional($item->tanggal_masuk)->format('Y-m-d') }}" required></label><label class="form-group"><span class="form-label">Estimasi Selesai</span><input class="form-control" type="date" name="estimasi_selesai" value="{{ optional($item->estimasi_selesai)->format('Y-m-d') }}"></label></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Berkas</button><button class="btn-back" type="button" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>
<x-ui.modal id="berkas-status-{{ $item->id }}" title="Perbarui Status Berkas" icon="bi-arrow-repeat">
<form method="POST" action="{{ route('cs.berkas.updateStatus', $item->id) }}">@csrf @method('PUT')
    <input type="hidden" name="_modal" value="berkas-status-{{ $item->id }}">
    <label class="form-group"><span class="form-label">Status Baru</span><select class="form-select" name="status_berkas" required><option value="Diterima" @selected($item->status_berkas === 'Diterima')>Diterima</option><option value="Diproses" @selected($item->status_berkas === 'Diproses')>Diproses</option><option value="Selesai" @selected($item->status_berkas === 'Selesai')>Selesai</option></select></label><label class="form-group"><span class="form-label">Keterangan</span><textarea class="form-control" name="keterangan" rows="3" placeholder="Catatan perubahan status (opsional)"></textarea></label>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-check2"></i>Simpan Status</button><button class="btn-back" type="button" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>
@endforeach
@endsection
