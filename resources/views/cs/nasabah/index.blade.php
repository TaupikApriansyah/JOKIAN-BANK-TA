@extends('layouts.cs')
@section('title', 'Data Nasabah')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-people-fill"></i>Data Nasabah</h1><p class="page-subtitle">Data identitas dan riwayat layanan nasabah.</p></div><button type="button" class="btn-add" data-modal-open="nasabah-create-modal"><i class="bi bi-person-plus"></i>Tambah Nasabah</button></header>
    <x-ui.flash />
    <section class="search-box"><form class="search-form" action="{{ route('cs.nasabah.index') }}"><div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau nomor telepon"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>@if(request('search'))<a class="btn-reset" href="{{ route('cs.nasabah.index') }}">Reset</a>@endif</form></section>
    @if(request('search'))<p class="search-result-info"><i class="bi bi-info-circle"></i> Menampilkan {{ $nasabah->count() }} hasil untuk “{{ request('search') }}”.</p>@endif
    <section class="table-wrapper"><table class="table table-hover"><thead><tr><th>No</th><th>Nasabah</th><th>NIK</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr></thead><tbody>
        @forelse($nasabah as $item)
        <tr><td><span class="row-number">{{ $loop->iteration }}</span></td><td class="fw-semibold">{{ $item->nama_nasabah }}</td><td>{{ $item->nik }}</td><td>{{ $item->no_telepon }}</td><td>{{ \Illuminate\Support\Str::limit($item->alamat, 42) }}</td><td><div class="action-buttons"><a class="btn-action btn-info" href="{{ route('cs.nasabah.show', $item->id) }}"><i class="bi bi-clock-history"></i>Riwayat</a><button class="btn-action btn-edit" type="button" data-modal-open="nasabah-edit-{{ $item->id }}"><i class="bi bi-pencil"></i>Edit</button><form method="POST" action="{{ route('cs.nasabah.destroy', $item->id) }}" onsubmit="return confirm('Hapus nasabah ini?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td></tr>
        @empty<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data nasabah.</td></tr>@endforelse
    </tbody></table></section>
</div>

<x-ui.modal id="nasabah-create-modal" title="Tambah Nasabah" icon="bi-person-plus" size="lg">
<form method="POST" action="{{ route('cs.nasabah.store') }}">@csrf
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Nama Nasabah</span><input class="form-control" name="nama_nasabah" value="{{ old('nama_nasabah') }}" required></label><label class="form-group"><span class="form-label">NIK</span><input class="form-control" name="nik" inputmode="numeric" maxlength="16" value="{{ old('nik') }}" required></label><label class="form-group"><span class="form-label">No. Telepon</span><input class="form-control" name="no_telepon" value="{{ old('no_telepon') }}" required></label><label class="form-group span-2"><span class="form-label">Alamat</span><textarea class="form-control" name="alamat" rows="3" required>{{ old('alamat') }}</textarea></label></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Nasabah</button><button class="btn-back" type="button" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>

@foreach($nasabah as $item)
<x-ui.modal id="nasabah-edit-{{ $item->id }}" title="Edit Nasabah" icon="bi-pencil-square" size="lg">
<form method="POST" action="{{ route('cs.nasabah.update', $item->id) }}">@csrf @method('PUT')
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Nama Nasabah</span><input class="form-control" name="nama_nasabah" value="{{ $item->nama_nasabah }}" required></label><label class="form-group"><span class="form-label">NIK</span><input class="form-control" name="nik" inputmode="numeric" maxlength="16" value="{{ $item->nik }}" required></label><label class="form-group"><span class="form-label">No. Telepon</span><input class="form-control" name="no_telepon" value="{{ $item->no_telepon }}" required></label><label class="form-group span-2"><span class="form-label">Alamat</span><textarea class="form-control" name="alamat" rows="3" required>{{ $item->alamat }}</textarea></label></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Nasabah</button><button class="btn-back" type="button" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>
@endforeach
@endsection
