@extends('layouts.admin')

@section('title', 'Tambah User Baru')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tambah User Baru</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form User Baru</h3>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkap">
                        @error('name')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="Masukkan email aktif">
                        @error('email')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimal 12 karakter, kombinasi huruf, angka, dan simbol">
                        @error('password')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="reset" class="btn btn-default mr-2">Reset</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
