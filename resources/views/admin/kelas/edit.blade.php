{{--
    Halaman Edit Kelas (admin/kelas/edit.blade.php)
    Menampilkan form pembaruan data kelas dengan menggunakan partial template _form.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Edit Kelas')
@section('page_kicker', 'Akademik · Kelas')
@section('page_title', 'Edit Kelas')
@section('page_description', 'Perbarui informasi kelompok kelas belajar.')

@section('page_actions')
    <a href="{{ route('admin.kelas.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.kelas.update', $kelas) }}" method="POST" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Informasi Kelas</h2>
            <p>Perubahan akan diterapkan setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            @include('admin.kelas._form')
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.kelas.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
        </div>
    </form>
@endsection
