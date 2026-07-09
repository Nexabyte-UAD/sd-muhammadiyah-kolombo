@extends('layouts.admin')

@section('title', 'Edit Berita')
@section('page_kicker', 'Konten website · Berita')
@section('page_title', 'Edit Berita')
@section('page_description', 'Perbarui isi dan status publikasi berita.')

@section('page_actions')
    <a href="{{ route('admin.berita.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Informasi Berita</h2>
            <p>Perubahan akan diterapkan setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            @include('admin.berita._form', ['berita' => $berita])
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.berita.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
        </div>
    </form>
@endsection
