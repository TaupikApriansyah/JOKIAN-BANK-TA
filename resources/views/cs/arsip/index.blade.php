@extends('layouts.cs')
@section('title', 'Arsip Digital')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-archive-fill"></i>Arsip Digital</h1><p class="page-subtitle">Unggah arsip dan siapkan rekap resmi dalam Excel atau PDF.</p></div><div class="action-buttons"><a class="btn-download" href="{{ route('cs.arsip.export.excel') }}"><i class="bi bi-file-earmark-excel"></i>Unduh Excel</a><a class="btn-back" href="{{ route('cs.arsip.export.pdf') }}" download><i class="bi bi-printer"></i>Unduh PDF</a><button class="btn-add" type="button" data-modal-open="arsip-create-modal"><i class="bi bi-upload"></i>Upload Arsip</button></div></header>
    <x-ui.flash />
    <section class="search-box"><form class="search-form" action="{{ route('cs.arsip.index') }}"><div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="{{ request('search') }}" placeholder="Cari nama file, dokumen, atau nasabah"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>@if(request('search'))<a class="btn-reset" href="{{ route('cs.arsip.index') }}">Reset</a>@endif</form></section>
    <section class="table-wrapper"><table class="table table-hover"><thead><tr><th>No</th><th>Nasabah / Berkas</th><th>Jenis Dokumen</th><th>Nama File</th><th>Upload</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
        @forelse($arsips as $arsip)
        <tr><td><span class="row-number">{{ $loop->iteration }}</span></td><td><b>{{ $arsip->berkas?->nasabah?->nama_nasabah ?? '-' }}</b><small class="d-block text-muted">{{ $arsip->berkas?->jenis_layanan ?? '-' }}</small></td><td>{{ $arsip->jenis_dokumen }}</td><td>{{ \Illuminate\Support\Str::limit($arsip->nama_file, 32) }}</td><td>{{ optional($arsip->tanggal_upload)->format('d M Y') }}</td><td><span class="status-badge status-aktif">{{ $arsip->status_arsip }}</span></td><td><div class="action-buttons"><a class="btn-action btn-info" href="{{ route('cs.arsip.download', $arsip->id) }}"><i class="bi bi-download"></i>Unduh</a><button class="btn-action btn-edit" type="button" data-modal-open="arsip-edit-{{ $arsip->id }}"><i class="bi bi-pencil"></i>Edit</button><form method="POST" action="{{ route('cs.arsip.destroy', $arsip->id) }}" onsubmit="return confirm('Hapus arsip ini?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td></tr>
        @empty<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada arsip digital.</td></tr>@endforelse
    </tbody></table></section>
</div>

<x-ui.modal id="arsip-create-modal" title="Upload Arsip Digital" icon="bi-upload" size="lg">
<form method="POST" action="{{ route('cs.arsip.store') }}" enctype="multipart/form-data">@csrf
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Berkas</span><select class="form-select" name="berkas_id" required><option value="">Pilih berkas</option>@foreach($berkasList as $berkas)<option value="{{ $berkas->id }}">{{ $berkas->nasabah?->nama_nasabah }} · {{ $berkas->jenis_layanan }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Jenis Dokumen</span><input class="form-control" name="jenis_dokumen" placeholder="Contoh: KTP, NPWP, Sertifikat" required></label><div class="form-group span-2"><span class="form-label">File Arsip</span><x-ui.upload-field name="file" id="file-arsip-create" :required="true" help="PDF, JPG, PNG, DOC, atau DOCX · maks. 10 MB" /></div></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Arsip</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>

@foreach($arsips as $arsip)
<x-ui.modal id="arsip-edit-{{ $arsip->id }}" title="Edit Arsip" icon="bi-pencil-square" size="lg">
<form method="POST" action="{{ route('cs.arsip.update', $arsip->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="modal-form-grid"><label class="form-group"><span class="form-label">Berkas</span><select class="form-select" name="berkas_id" required>@foreach($berkasList as $berkas)<option value="{{ $berkas->id }}" @selected($arsip->berkas_id === $berkas->id)>{{ $berkas->nasabah?->nama_nasabah }} · {{ $berkas->jenis_layanan }}</option>@endforeach</select></label><label class="form-group"><span class="form-label">Jenis Dokumen</span><input class="form-control" name="jenis_dokumen" value="{{ $arsip->jenis_dokumen }}" required></label><label class="form-group"><span class="form-label">Tanggal Upload</span><input class="form-control" type="date" name="tanggal_upload" value="{{ optional($arsip->tanggal_upload)->format('Y-m-d') }}" required></label><div class="form-group span-2"><span class="form-label">Ganti File <small class="text-muted">(opsional)</small></span><x-ui.upload-field name="file" id="file-arsip-{{ $arsip->id }}" help="Kosongkan bila file lama tetap dipakai" /></div></div>
    <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Arsip</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
</form>
</x-ui.modal>
@endforeach
@endsection
