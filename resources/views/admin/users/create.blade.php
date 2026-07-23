@extends('layouts.admin')

@section('title','Tambah User | SIBERKAS')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-person-plus-fill"></i>
            Tambah User
        </div>
        <div class="page-subtitle">
            Tambahkan pengguna baru ke dalam sistem
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM -->
    <div class="card-form">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label">Nama</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                    <option value="cs" {{ old('role')=='cs'?'selected':'' }}>CS</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-save"></i>
                    Simpan User
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
