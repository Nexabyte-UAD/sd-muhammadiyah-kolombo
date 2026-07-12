{{--
    Halaman Sunting Akun Admin (admin/users/edit.blade.php)
    Menyediakan formulir pembaruan data pengguna admin (nama, username, email).
    Kolom password bersifat opsional (hanya diisi jika admin ingin memperbarui password saat ini).
--}}
@extends('layouts.admin')

@section('title', 'Edit User')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Data User</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">
                <x-admin-icon name="arrow-left" size="16" class="mr-1"/>
                Kembali
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-accent">
            <div class="card-header">
                <h3 class="card-title">Form Edit User</h3>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required placeholder="Masukkan username unik (hanya huruf, angka, -, _)">
                        @error('username')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="alert alert-info mt-4">
                        <h5><x-admin-icon name="info" size="17" class="mr-1"/> Ubah Password</h5>
                        Biarkan kosong jika tidak ingin mengubah password saat ini.
                    </div>

                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 12 karakter, kombinasi huruf, angka, dan simbol">
                        @error('password')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <x-admin-icon name="save" size="16" class="mr-1"/>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
