@extends('adminlte::page')

@section('title', 'Tambah Ekstrakurikuler')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tambah Ekstrakurikuler</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Ekstrakurikuler</h3>
            </div>
            <form action="{{ route('admin.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama kegiatan">
                        @error('nama')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pembina">Pembina</label>
                        <input type="text" name="pembina" id="pembina" class="form-control @error('pembina') is-invalid @enderror" value="{{ old('pembina') }}" placeholder="Masukkan nama pembina">
                        @error('pembina')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="jadwal">Jadwal <span class="text-danger">*</span></label>
                        <input type="text" name="jadwal" id="jadwal" class="form-control @error('jadwal') is-invalid @enderror" value="{{ old('jadwal') }}" required placeholder="Contoh: Setiap Sabtu, 15:00">
                        @error('jadwal')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" required placeholder="Deskripsi kegiatan...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto Kegiatan (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih file</label>
                        </div>
                        @error('foto')
                            <span class="error invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $(function () {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endpush
