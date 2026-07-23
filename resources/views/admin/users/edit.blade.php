@extends('layouts.admin')

@section('title','Edit User | SIBERKAS')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-pencil-square"></i>
            Edit User
        </div>
        <div class="page-subtitle">
            Perbarui informasi pengguna sistem
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
        <form method="POST" action="{{ route('admin.users.update',$user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="form-label">Nama</label>
                <input type="text"
                       name="name"
                       value="{{ old('name',$user->name) }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email',$user->email) }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin"
                        {{ old('role',$user->role)=='admin'?'selected':'' }}>
                        Admin
                    </option>
                    <option value="cs"
                        {{ old('role',$user->role)=='cs'?'selected':'' }}>
                        CS
                    </option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-update">
                    <i class="bi bi-check-lg"></i>
                    Update User
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
