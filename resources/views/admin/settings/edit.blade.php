@extends('layouts.admin')

@section('title', 'Sistem & Beranda')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Konfigurasi Sistem Terpadu</h1>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
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

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-auto-format-notice />
            
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tabs-identitas-tab" data-toggle="pill" href="#tabs-identitas" role="tab" aria-controls="tabs-identitas" aria-selected="true">
                                <i class="fas fa-id-badge mr-1"></i> Identitas & Logo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tabs-beranda-tab" data-toggle="pill" href="#tabs-beranda" role="tab" aria-controls="tabs-beranda" aria-selected="false">
                                <i class="fas fa-home mr-1"></i> Pengaturan Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tabs-kontak-tab" data-toggle="pill" href="#tabs-kontak" role="tab" aria-controls="tabs-kontak" aria-selected="false">
                                <i class="fas fa-address-book mr-1"></i> Kontak & Footer
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        
                        <!-- TAB: Identitas & Logo -->
                        <div class="tab-pane fade show active" id="tabs-identitas" role="tabpanel" aria-labelledby="tabs-identitas-tab">
                            <h4 class="mb-3 text-primary">Pengenalan Institusi</h4>
                            <hr>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Nama Institusi / Sekolah</label>
                                        <input type="text" class="form-control" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? '' }}" required>
                                        <small class="form-text text-muted">Akan ditampilkan di tab judul, header, dan atribusi footer bawah.</small>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Logo Utama Terkini</label>
                                        <div class="mb-2">
                                            @if(isset($settings['logo']) && $settings['logo'])
                                                <div class="p-2 border d-inline-block bg-light">
                                                    <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" style="max-height: 70px;">
                                                </div>
                                            @else
                                                <span class="text-muted"><i class="fas fa-image mr-1"></i> Belum ada logo</span>
                                            @endif
                                        </div>
                                        <div class="custom-file mt-2">
                                            <input type="file" class="custom-file-input" name="logo" id="logo" accept="image/*">
                                            <label class="custom-file-label" for="logo">Pilih logo baru</label>
                                        </div>
                                        <small class="form-text text-muted">Biarkan kosong jika tidak merubah. Gunakan format PNG transparan untuk hasil terbaik (Maks 2MB).</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- TAB: Pengaturan Beranda -->
                        <div class="tab-pane fade" id="tabs-beranda" role="tabpanel" aria-labelledby="tabs-beranda-tab">
                            <h4 class="mb-3 text-primary">Desain Halaman Depan Web</h4>
                            <hr>
                            
                            <!-- Hero Banners Grid -->
                            <div class="row">
                                <!-- Banner 1 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-image text-primary mr-1"></i> Banner Utama 1</label>
                                        <div class="mb-2">
                                            @if(isset($settings['hero_image']) && $settings['hero_image'])
                                                <img src="{{ asset('storage/' . $settings['hero_image']) }}" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;" alt="Hero 1">
                                            @else
                                                <div class="bg-light border d-flex align-items-center justify-content-center text-muted" style="height: 120px;">
                                                    <i class="fas fa-image fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="hero_image" id="hero_image" accept="image/*">
                                            <label class="custom-file-label text-truncate" for="hero_image">Pilih Banner 1</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Banner 2 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-image text-primary mr-1"></i> Banner Slide 2 (Opsional)</label>
                                        <div class="mb-2">
                                            @if(isset($settings['hero_image_2']) && $settings['hero_image_2'])
                                                <img src="{{ asset('storage/' . $settings['hero_image_2']) }}" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;" alt="Hero 2">
                                            @else
                                                <div class="bg-light border d-flex align-items-center justify-content-center text-muted" style="height: 120px;">
                                                    <i class="fas fa-image fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="hero_image_2" id="hero_image_2" accept="image/*">
                                            <label class="custom-file-label text-truncate" for="hero_image_2">Pilih Banner 2</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Banner 3 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-image text-primary mr-1"></i> Banner Slide 3 (Opsional)</label>
                                        <div class="mb-2">
                                            @if(isset($settings['hero_image_3']) && $settings['hero_image_3'])
                                                <img src="{{ asset('storage/' . $settings['hero_image_3']) }}" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;" alt="Hero 3">
                                            @else
                                                <div class="bg-light border d-flex align-items-center justify-content-center text-muted" style="height: 120px;">
                                                    <i class="fas fa-image fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="hero_image_3" id="hero_image_3" accept="image/*">
                                            <label class="custom-file-label text-truncate" for="hero_image_3">Pilih Banner 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted mb-4">
                                Resolusi disarankan: minimum <strong>1920x800px</strong> (Rasio Landscape).<br>
                                Jika mengunggah lebih dari 1 gambar, banner di beranda otomatis berubah menjadi slide carousel yang bergerak secara berkala.
                            </small>

                            <div class="row mt-4">
                                <!-- Area Penamaan -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Slogan Generasi Beranda (Teks Warna Biru)</label>
                                        <input type="text" class="form-control" name="beranda_profil_judul" value="{{ $settings['beranda_profil_judul'] ?? '' }}" placeholder="Islami & Berprestasi">
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Lengkap Kepala Sekolah</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                            </div>
                                            <input type="text" class="form-control" name="kepsek_nama" value="{{ $settings['kepsek_nama'] ?? '' }}" placeholder="Drs. Fulan, M.Pd...">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Area Textarea -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Deskripsi Ringkas Sekolah (Dibawah Banner)</label>
                                        <textarea class="form-control" name="beranda_profil_teks" rows="3" style="resize: none;">{{ $settings['beranda_profil_teks'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Teks Pidato Sambutan Kepala Sekolah</label>
                                        <textarea class="form-control" name="kepsek_sambutan" rows="4" style="resize: none;">{{ $settings['kepsek_sambutan'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Kontak & Footer -->
                        <div class="tab-pane fade" id="tabs-kontak" role="tabpanel" aria-labelledby="tabs-kontak-tab">
                            <h4 class="mb-3 text-primary">Kontribusi Publik & Sosial</h4>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Telepon / WhatsApp</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone text-success"></i></span>
                                            </div>
                                            <input type="text" class="form-control" name="telepon" value="{{ $settings['telepon'] ?? '' }}" placeholder="+62 274...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email Kantor & Pusat Resolusi</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope text-danger"></i></span>
                                            </div>
                                            <input type="email" class="form-control" name="email" value="{{ $settings['email'] ?? '' }}" placeholder="info@sekolah.sch.id">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Alamat Gedung Lengkap</label>
                                        <textarea class="form-control" name="alamat" rows="2">{{ $settings['alamat'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3">Tautan Sosial Media Platform</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-facebook text-primary"></i></span>
                                            </div>
                                            <input type="url" class="form-control" name="facebook" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-instagram text-fuchsia"></i></span>
                                            </div>
                                            <input type="url" class="form-control" name="instagram" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-youtube text-danger"></i></span>
                                            </div>
                                            <input type="url" class="form-control" name="youtube" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-tiktok text-dark"></i></span>
                                            </div>
                                            <input type="url" class="form-control" name="tiktok" value="{{ $settings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Card Footer / Simpan Area -->
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Sinkronisasi Sistem
                    </button>
                </div>
            </div>
        </form>
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
