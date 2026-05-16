@extends('adminlte::page')

@section('title', 'Edit Pegawai')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit {{ ucfirst($guru->tipe) }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.guru-staff.index', ['tipe' => $guru->tipe]) }}" class="btn btn-default">
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
                <h3 class="card-title">Informasi Dasar {{ ucfirst($guru->tipe) }}</h3>
            </div>
            <form action="{{ route('admin.guru-staff.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $guru->nama) }}" required placeholder="Contoh: Ahmad Dahlan, S.Pd">
                                @error('nama')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $guru->nip) }}" placeholder="Nomor Induk Pegawai (Opsional)">
                                @error('nip')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <input type="hidden" name="tipe" value="{{ $guru->tipe }}">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jabatan">Jabatan Pokok <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $guru->jabatan) }}" required placeholder="Contoh: Guru Kelas, Wali Kelas, Bendahara">
                                @error('jabatan')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mapel">Pendidikan Pokok / Mapel (Opsional)</label>
                                <input type="text" name="mapel" id="mapel" class="form-control @error('mapel') is-invalid @enderror" value="{{ old('mapel', $guru->mapel) }}" placeholder="Contoh: Matematika, Bahasa Indonesia">
                                <small class="form-text text-muted">Dapat dikosongkan jika staf administrasi atau tidak punya mapel spesifik.</small>
                                @error('mapel')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Saat Ini</label>
                                <div class="mb-2">
                                    @if($guru->foto)
                                        <img src="{{ asset('storage/' . $guru->foto) }}" class="img-thumbnail" style="max-height: 100px;" alt="Preview">
                                    @else
                                        <span class="text-muted"><i class="fas fa-image mr-1"></i> Tidak ada foto</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="foto">Ganti Foto Profil</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" accept="image/jpeg,image/png,image/jpg">
                                    <label class="custom-file-label" for="foto">Pilih file baru</label>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG. Abaikan jika tidak ingin mengubah foto lama.</small>
                                @error('foto')
                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update {{ ucfirst($guru->tipe) }}</button>
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
