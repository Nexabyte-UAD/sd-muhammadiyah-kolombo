@extends('layouts.auth')

@section('title', 'Lupa Password')
@section('heading', 'Lupa Password')
@section('description', 'Masukkan email akun admin untuk menerima tautan reset password.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   class="@error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   autocomplete="email"
                   inputmode="email"
                   autofocus required>
            @error('email')<div class="auth-error" role="alert">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="auth-submit">Kirim Tautan Reset</button>
        <a href="{{ route('login') }}" class="auth-secondary-link">Kembali ke Login Admin</a>
    </form>
@endsection
