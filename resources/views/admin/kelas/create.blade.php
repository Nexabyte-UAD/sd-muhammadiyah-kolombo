@extends('adminlte::page')

@section('title', 'Tambah Kelas')

@section('content_header')
    <h1 class="m-0 text-dark">Tambah Kelas</h1>
@stop

@section('content')
<div class="card card-primary">
    <form action="{{ route('admin.kelas.store') }}" method="POST">
        @csrf
        <div class="card-body">
            @include('admin.kelas._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@stop
