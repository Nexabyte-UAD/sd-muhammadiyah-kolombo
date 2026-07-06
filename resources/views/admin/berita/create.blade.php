@extends('adminlte::page')

@section('title', 'Tambah Berita')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tambah Berita</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.berita.index') }}" class="btn btn-default">
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
                <h3 class="card-title">Form Berita</h3>
            </div>
            <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="form-group">
                        <label for="judul">Judul Berita <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required placeholder="Masukkan judul berita">
                        @error('judul')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="isi">Isi Berita <span class="text-danger">*</span></label>
                        <textarea name="isi" id="isi" class="form-control @error('isi') is-invalid @enderror" rows="6" required placeholder="Tuliskan isi berita di sini...">{{ old('isi') }}</textarea>
                        @error('isi')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status Publikasi <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (Tampil di Website)</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Belum Ditampilkan)</option>
                        </select>
                        @error('status')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="gambar" class="custom-file-input @error('gambar') is-invalid @enderror" id="gambar" accept="image/*">
                            <label class="custom-file-label" for="gambar">Pilih file</label>
                        </div>
                        @error('gambar')
                            <span class="error invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $(function () {
        // bsCustomFileInput.init(); // Uncomment if you have bs-custom-file-input included in AdminLTE
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endpush
