@extends('adminlte::page')

@section('title', 'Edit Ekstrakurikuler')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Ekstrakurikuler</h1>
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
                <h3 class="card-title">Form Edit Ekstrakurikuler</h3>
            </div>
            <form action="{{ route('admin.ekstrakurikuler.update', $ekstrakurikuler->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $ekstrakurikuler->nama) }}" required>
                        @error('nama')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pembina">Pembina</label>
                        <input type="text" name="pembina" id="pembina" class="form-control @error('pembina') is-invalid @enderror" value="{{ old('pembina', $ekstrakurikuler->pembina) }}">
                        @error('pembina')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="jadwal">Jadwal <span class="text-danger">*</span></label>
                        <input type="text" name="jadwal" id="jadwal" class="form-control @error('jadwal') is-invalid @enderror" value="{{ old('jadwal', $ekstrakurikuler->jadwal) }}" required>
                        @error('jadwal')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" required>{{ old('deskripsi', $ekstrakurikuler->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Foto Saat Ini</label>
                        <div class="mt-2 mb-3">
                            @if($ekstrakurikuler->foto)
                                <img src="{{ asset('storage/' . $ekstrakurikuler->foto) }}" height="100" class="img-thumbnail">
                            @else
                                <span class="text-muted"><i class="fas fa-image mr-1"></i> Tidak ada foto</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="foto">Ganti Foto (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" accept="image/*">
                            <label class="custom-file-label" for="foto">Pilih file baru</label>
                        </div>
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                        @error('foto')
                            <span class="error invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update</button>
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
