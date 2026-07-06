@extends('layouts.admin')

@section('title', 'Akun Admin')
@section('page_kicker', 'Pengaturan akun')
@section('page_title', 'Akun Admin')
@section('page_description', 'Perbarui identitas login dan password administrator.')

@section('content')
    <form action="{{ route('admin.account.update') }}" method="POST" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-card-header">
            <h2>Informasi Akun</h2>
            <p>Role administrator tidak dapat diubah dari halaman ini.</p>
        </div>

        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field">
                    <label for="name" class="form-label">Nama Administrator <span>*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control-admin @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" autocomplete="name" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="email" class="form-label">Alamat Email <span>*</span></label>
                    <input type="email" name="email" id="email"
                           class="form-control-admin @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" autocomplete="username" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <div class="account-security-note">
                        <strong>Ganti password</strong>
                        <span>Biarkan kedua kolom berikut kosong jika password tidak ingin diubah.</span>
                    </div>
                </div>

                <div class="form-field">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password"
                           class="form-control-admin @error('password') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control-admin" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="form-card-footer">
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
        </div>
    </form>
@endsection
