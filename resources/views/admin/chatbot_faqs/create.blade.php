{{--
    Halaman Tambah FAQ Chatbot (admin/chatbot_faqs/create.blade.php)
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Tambah FAQ')
@section('page_kicker', 'Sistem & Pesan · FAQ Chatbot')
@section('page_title', 'Tambah FAQ')
@section('page_description', 'Tambahkan pertanyaan dan jawaban baru untuk asisten chatbot sekolah.')

@section('page_actions')
    <a href="{{ route('admin.chatbot-faqs.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.chatbot-faqs.store') }}" method="POST" class="form-card">
        @csrf
        <div class="form-card-header">
            <h2>Data FAQ Chatbot</h2>
            <p>Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="form-card-body">
            @include('admin.chatbot_faqs._form')
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.chatbot-faqs.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan FAQ</button>
        </div>
    </form>
@endsection
