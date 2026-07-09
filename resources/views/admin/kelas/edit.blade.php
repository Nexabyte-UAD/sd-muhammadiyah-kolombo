@extends('layouts.admin')

@section('title', 'Edit Kelas')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Kelas</h1>
@stop

@section('content')
<div class="card card-accent">
    <form action="{{ route('admin.kelas.update', $kelas) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('admin.kelas._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update</button>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@stop
