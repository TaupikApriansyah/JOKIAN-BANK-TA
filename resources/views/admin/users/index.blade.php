@extends('layouts.admin')
@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid">
    <header class="page-header"><div><h1 class="page-title"><i class="bi bi-people-fill"></i>Manajemen User</h1><p class="page-subtitle">Tambah dan perbarui akun tanpa berpindah halaman.</p></div><button type="button" class="btn-add" data-modal-open="user-create-modal"><i class="bi bi-person-plus"></i>Tambah User</button></header>
    <x-ui.flash />
    <section class="table-wrapper">
        <table class="table table-hover"><thead><tr><th>No</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
        @forelse($users as $user)
            <tr><td><span class="row-number">{{ $users->firstItem() + $loop->index }}</span></td><td class="fw-semibold">{{ $user->name }}</td><td>{{ $user->email }}</td><td><span class="role-badge {{ $user->role }}">{{ strtoupper($user->role) }}</span></td><td><span class="status-badge status-{{ $user->status }}">{{ ucfirst($user->status) }}</span></td><td><div class="action-buttons"><button type="button" class="btn-action btn-edit" data-modal-open="user-edit-{{ $user->id }}"><i class="bi bi-pencil"></i>Edit</button><form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td></tr>
        @empty
            <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data user.</td></tr>
        @endforelse
        </tbody></table>
    </section>
    <div class="mt-4">{{ $users->links() }}</div>
</div>

<x-ui.modal id="user-create-modal" title="Tambah User" icon="bi-person-plus">
    <form method="POST" action="{{ route('admin.users.store') }}">@csrf
        <div class="modal-form-grid"><label class="form-group"><span class="form-label">Nama</span><input class="form-control" name="name" value="{{ old('name') }}" required></label><label class="form-group"><span class="form-label">Email</span><input class="form-control" name="email" type="email" value="{{ old('email') }}" required></label><label class="form-group"><span class="form-label">Role</span><select class="form-select" name="role" required><option value="cs">Customer Service</option><option value="admin">Admin</option><option value="akuntan">Akuntan</option></select></label><label class="form-group"><span class="form-label">Status</span><select class="form-select" name="status" required><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></label><label class="form-group span-2"><span class="form-label">Password <small class="text-muted">(min. 8 karakter)</small></span><input class="form-control" name="password" type="password" minlength="8" required></label></div>
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan User</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
</x-ui.modal>

@foreach($users as $user)
<x-ui.modal id="user-edit-{{ $user->id }}" title="Edit User" icon="bi-pencil-square">
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">@csrf @method('PUT')
        <div class="modal-form-grid"><label class="form-group"><span class="form-label">Nama</span><input class="form-control" name="name" value="{{ $user->name }}" required></label><label class="form-group"><span class="form-label">Email</span><input class="form-control" name="email" type="email" value="{{ $user->email }}" required></label><label class="form-group"><span class="form-label">Role</span><select class="form-select" name="role" required><option value="cs" @selected($user->role === 'cs')>Customer Service</option><option value="admin" @selected($user->role === 'admin')>Admin</option><option value="akuntan" @selected($user->role === 'akuntan')>Akuntan</option></select></label><label class="form-group"><span class="form-label">Status</span><select class="form-select" name="status" required><option value="aktif" @selected($user->status === 'aktif')>Aktif</option><option value="nonaktif" @selected($user->status === 'nonaktif')>Nonaktif</option></select></label></div>
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update User</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
</x-ui.modal>
@endforeach
@endsection
