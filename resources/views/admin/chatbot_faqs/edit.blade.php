{{--
    Halaman Edit FAQ Chatbot (admin/chatbot_faqs/edit.blade.php)
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Edit FAQ')
@section('page_kicker', 'Sistem & Pesan · FAQ Chatbot')
@section('page_title', 'Edit FAQ')
@section('page_description', 'Perbarui pertanyaan atau jawaban FAQ untuk asisten chatbot sekolah.')

@section('page_actions')
    <a href="{{ route('admin.chatbot-faqs.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.chatbot-faqs.update', $chatbotFaq) }}" method="POST" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Data FAQ Chatbot</h2>
            <p>Kolom bertanda bintang wajib diisi. Digunakan <strong>{{ $chatbotFaq->usage_count }}</strong> kali oleh chatbot.</p>
        </div>
        <div class="form-card-body">
            @include('admin.chatbot_faqs._form')
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.chatbot-faqs.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Perbarui FAQ</button>
        </div>
    </form>
@endsection
