@extends('layouts.admin')

@section('title', 'Edit Prestasi')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Prestasi</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.prestasi.index') }}" class="btn btn-default">
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
                <h3 class="card-title">Form Edit Prestasi</h3>
            </div>
            <form action="{{ route('admin.prestasi.update', $prestasi->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="form-group">
                        <label for="judul">Nama Lomba <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $prestasi->judul) }}" required>
                        @error('judul')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                            @foreach($kategoriPrestasi as $value => $label)
                                <option value="{{ $value }}" @selected(old('kategori', $prestasi->kategori) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kategori')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="siswa_id">Nama Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" id="siswa_id" class="form-control @error('siswa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}" @selected((string) old('siswa_id', $prestasi->siswa_id) === (string) $siswa->id)>
                                    {{ $siswa->nama }}{{ $siswa->kelas ? ' — '.$siswa->kelas : ' — Alumni' }}
                                </option>
                            @endforeach
                        </select>
                        @error('siswa_id')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="prestasi_medali">Prestasi / Medali <span class="text-danger">*</span></label>
                        <input type="text" name="prestasi_medali" id="prestasi_medali" class="form-control @error('prestasi_medali') is-invalid @enderror" value="{{ old('prestasi_medali', $prestasi->prestasi_medali) }}" required>
                        @error('prestasi_medali')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="penyelenggara">Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" name="penyelenggara" id="penyelenggara" class="form-control @error('penyelenggara') is-invalid @enderror" value="{{ old('penyelenggara', $prestasi->penyelenggara) }}" required>
                        @error('penyelenggara')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $prestasi->tanggal ? \Carbon\Carbon::parse($prestasi->tanggal)->format('Y-m-d') : '') }}" required>
                        @error('tanggal')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Keterangan / Tingkat <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" required>{{ old('deskripsi', $prestasi->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <span class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Foto Saat Ini</label>
                        <div class="mt-2 mb-3">
                            @if($prestasi->gambar)
                                <img src="{{ asset('storage/' . $prestasi->gambar) }}" height="100" class="img-thumbnail">
                            @else
                                <span class="text-muted"><i class="fas fa-image mr-1"></i> Tidak ada foto</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Ganti Foto (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="gambar" class="custom-file-input @error('gambar') is-invalid @enderror" id="gambar" accept="image/*">
                            <label class="custom-file-label" for="gambar">Pilih file baru</label>
                        </div>
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                        @error('gambar')
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
