{{--
    Halaman Tambah Kelas Baru (admin/kelas/create.blade.php)
    Menampilkan form pembuatan data kelas baru dengan memanfaatkan partial template _form.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Tambah Kelas')
@section('page_kicker', 'Akademik · Kelas')
@section('page_title', 'Tambah Kelas')
@section('page_description', 'Tambahkan kelompok kelas belajar baru.')

@section('page_actions')
    <a href="{{ route('admin.kelas.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.kelas.store') }}" method="POST" class="form-card">
        @csrf
        <div class="form-card-header">
            <h2>Informasi Kelas</h2>
            <p>Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="form-card-body">
            @include('admin.kelas._form')
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.kelas.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Kelas</button>
        </div>
    </form>
@endsection
