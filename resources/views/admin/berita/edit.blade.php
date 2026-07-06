@extends('adminlte::page')

@section('title', 'Edit Berita')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Berita</h1>
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
                <h3 class="card-title">Form Edit Berita</h3>
            </div>
            <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="form-group">
                        <label for="judul">Judul Berita <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $berita->judul) }}" required>
                        @error('judul')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', \Carbon\Carbon::parse($berita->tanggal)->format('Y-m-d')) }}" required>
                        @error('tanggal')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="isi">Isi Berita <span class="text-danger">*</span></label>
                        <textarea name="isi" id="isi" class="form-control @error('isi') is-invalid @enderror" rows="6" required>{{ old('isi', $berita->isi) }}</textarea>
                        @error('isi')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status Publikasi <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>Published (Tampil di Website)</option>
                            <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>Draft (Belum Ditampilkan)</option>
                        </select>
                        @error('status')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Saat Ini</label>
                        <div class="mt-2 mb-3">
                            @if($berita->gambar)
                                <img src="{{ asset('storage/' . $berita->gambar) }}" alt="gambar" class="img-thumbnail" style="max-height: 200px;">
                            @else
                                <span class="text-muted"><i class="fas fa-image mr-1"></i> Tidak ada gambar</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Ganti Gambar (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="gambar" class="custom-file-input @error('gambar') is-invalid @enderror" id="gambar" accept="image/*">
                            <label class="custom-file-label" for="gambar">Pilih file baru</label>
                        </div>
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        @error('gambar')
                            <span class="error invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update Berita</button>
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
