@extends('layouts.auth')

@section('title', 'Reset Password')
@section('heading', 'Buat Password Baru')
@section('description', 'Gunakan minimal 12 karakter dengan huruf besar, huruf kecil, angka, dan simbol.')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-field">
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   class="@error('email') is-invalid @enderror"
                   value="{{ old('email', $email) }}"
                   autocomplete="username" required>
            @error('email')<div class="auth-error" role="alert">{{ $message }}</div>@enderror
        </div>

        <div class="auth-field">
            <label for="password">Password Baru</label>
            <div class="auth-password-wrap">
                <input type="password" name="password" id="password"
                       class="@error('password') is-invalid @enderror"
                       autocomplete="new-password" required>
                <button type="button" class="auth-password-toggle"
                        data-password-toggle="password"
                        aria-label="Tampilkan password"
                        aria-pressed="false">
                    <span class="password-show">Tampilkan</span>
                    <span class="password-hide">Sembunyikan</span>
                </button>
            </div>
            @error('password')<div class="auth-error" role="alert">{{ $message }}</div>@enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   autocomplete="new-password" required>
        </div>

        <button type="submit" class="auth-submit">Simpan Password Baru</button>
    </form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
            button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
</script>
@endpush
