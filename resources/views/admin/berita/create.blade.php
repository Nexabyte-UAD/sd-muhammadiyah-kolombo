{{--
    Halaman Tulis Berita Baru (admin/berita/create.blade.php)
    Menyediakan interface untuk menulis berita/pengumuman sekolah baru dengan menyertakan partial _form.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Tambah Berita')
@section('page_kicker', 'Konten website · Berita')
@section('page_title', 'Tambah Berita')
@section('page_description', 'Tulis informasi baru untuk ditampilkan pada website sekolah.')

@section('page_actions')
    <a href="{{ route('admin.berita.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        <div class="form-card-header">
            <h2>Informasi Berita</h2>
            <p>Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="form-card-body">
            @include('admin.berita._form')
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.berita.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Berita</button>
        </div>
    </form>
@endsection
