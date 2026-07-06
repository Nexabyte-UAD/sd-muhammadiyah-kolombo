@extends('adminlte::page')

@section('title', 'Edit ' . $profil->judul)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit: {{ $profil->judul }}</h1>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5><i class="icon fas fa-ban mr-1"></i> Terjadi Kesalahan!</h5>
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Profil Sekolah</h3>
            </div>
            <form action="{{ route('admin.profil-sekolah.updateType', $type) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-auto-format-notice />
                <div class="card-body">
                    <div class="row">
                        @if($type === 'akreditasi')
                            <!-- Khusus Akreditasi: Hanya Upload Gambar Sertifikat -->
                            <div class="col-md-12">
                                <h4 class="mb-3 text-primary"><i class="fas fa-file-signature mr-2"></i>Sertifikat Akreditasi</h4>
                                <hr>
                                
                                <input type="hidden" name="judul" value="{{ old('judul', $profil->judul ?: 'Sertifikat Akreditasi') }}">
                                <input type="hidden" name="konten" value="{{ old('konten', $profil->konten ?: '-') }}">

                                <div class="form-group">
                                    <label>Gambar Sertifikat Saat Ini</label>
                                    <div class="bg-light p-3 border text-center mb-3 rounded">
                                        @if($profil->gambar)
                                            <img src="{{ asset('storage/' . $profil->gambar) }}" class="img-fluid img-thumbnail shadow-sm" style="max-height: 450px;" alt="Sertifikat Akreditasi">
                                        @else
                                            <div class="py-5 text-muted">
                                                <i class="fas fa-file-image fa-4x mb-3 text-secondary"></i>
                                                <p class="mb-0">Belum ada file gambar sertifikat yang diunggah.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="gambar">Unggah Gambar Sertifikat Baru</label>
                                    <div class="custom-file">
                                        <input type="file" name="gambar" class="custom-file-input @error('gambar') is-invalid @enderror" id="gambar" accept="image/jpeg,image/png,image/jpg" required>
                                        <label class="custom-file-label" for="gambar">Pilih file gambar sertifikat</label>
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal ukuran file: 2MB. Mengunggah gambar baru akan menimpa sertifikat lama.</small>
                                    @error('gambar')
                                        <span class="error invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="alert alert-info mt-4">
                                    <h5><i class="icon fas fa-info"></i> Info!</h5>
                                    Setelah gambar sertifikat berhasil diganti, refresh halaman publik (frontend) Anda untuk melihat perubahannya.
                                </div>
                            </div>
                        @else
                            <!-- Sisi Kiri: Teks -->
                            <div class="{{ $type === 'visi_misi' ? 'col-md-12' : 'col-md-7' }}">
                                <h4 class="mb-3 text-primary"><i class="fas fa-file-alt mr-2"></i>Konten Teks</h4>
                                <hr>
                                <div class="form-group">
                                    <label for="judul">Judul Halaman <span class="text-danger">*</span></label>
                                    <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $profil->judul) }}" required>
                                    @error('judul')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="konten">Isi Konten / Penjelasan <span class="text-danger">*</span></label>
                                    <textarea name="konten" id="konten" class="form-control @error('konten') is-invalid @enderror" rows="12" placeholder="Ketik isi dari halaman ini..." required>{{ old('konten', $profil->konten) }}</textarea>
                                    <small class="form-text text-muted">Gunakan tombol <code>Enter</code> pada keyboard untuk memisahkan paragraf satu dengan yang lainnya.</small>
                                    @error('konten')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            @if($type !== 'visi_misi')
                            <!-- Sisi Kanan: Gambar -->
                            <div class="col-md-5">
                                <h4 class="mb-3 text-primary"><i class="fas fa-image mr-2"></i>Media Gambar</h4>
                                <hr>
                                <div class="form-group">
                                    <label>Gambar Saat Ini</label>
                                    <div class="bg-light p-3 border text-center mb-3">
                                        @if($profil->gambar)
                                            <img src="{{ asset('storage/' . $profil->gambar) }}" class="img-fluid img-thumbnail" style="max-height: 250px;" alt="Preview">
                                        @else
                                            <div class="py-5 text-muted">
                                                <i class="fas fa-camera-retro fa-3x mb-3"></i>
                                                <p class="mb-0">Belum ada gambar yang diunggah.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="gambar">Unggah Gambar Baru</label>
                                    <div class="custom-file">
                                        <input type="file" name="gambar" class="custom-file-input @error('gambar') is-invalid @enderror" id="gambar" accept="image/jpeg,image/png,image/jpg">
                                        <label class="custom-file-label" for="gambar">Pilih file</label>
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, PNG. Maksimal ukuran file: 2MB. Mengunggah gambar baru akan otomatis menimpa gambar lama.</small>
                                    @error('gambar')
                                        <span class="error invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <h5><i class="icon fas fa-info"></i> Info!</h5>
                                    Setelah gambar berhasil diganti, refresh halaman publik (frontend) Anda untuk melihat perubahannya.
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
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
