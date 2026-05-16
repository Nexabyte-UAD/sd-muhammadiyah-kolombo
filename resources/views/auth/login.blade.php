@extends('adminlte::auth.login')

@section('auth_header', 'Login Administrator')

@section('login_url', route('login'))

{{-- You can optionally add reCAPTCHA back here if needed by overriding auth_body --}}
