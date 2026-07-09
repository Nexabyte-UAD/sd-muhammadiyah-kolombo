@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page_kicker', 'Administrasi')
@section('page_title', 'Pengaturan Sistem')
@section('page_description', 'Kelola identitas sekolah, tampilan beranda, kontak, dan tautan publik website.')

@section('content')
    <div class="settings-layout">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan.</strong>
                <ul class="mb-0 mt-2 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-auto-format-notice />

            <ul class="nav settings-tabs" id="settings-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tabs-identitas-tab" data-toggle="pill" href="#tabs-identitas" role="tab" aria-controls="tabs-identitas" aria-selected="true">
                        Identitas Sekolah
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tabs-beranda-tab" data-toggle="pill" href="#tabs-beranda" role="tab" aria-controls="tabs-beranda" aria-selected="false">
                        Pengaturan Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tabs-kontak-tab" data-toggle="pill" href="#tabs-kontak" role="tab" aria-controls="tabs-kontak" aria-selected="false">
                        Kontak & Footer
                    </a>
                </li>
            </ul>

            <div class="settings-panel">
                <div class="tab-content" id="settings-tabs-content">
                    <div class="tab-pane fade show active" id="tabs-identitas" role="tabpanel" aria-labelledby="tabs-identitas-tab">
                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Identitas Institusi</h2>
                                <p>Informasi dasar yang tampil pada judul halaman, header, dan footer website.</p>
                            </div>

                            <div class="settings-grid">
                                <div class="settings-field settings-field-wide">
                                    <label for="nama_sekolah">Nama Institusi / Sekolah</label>
                                    <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? '' }}" required>
                                    <small class="form-text text-muted">Akan ditampilkan di tab judul, header, dan atribusi footer bawah.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tabs-beranda" role="tabpanel" aria-labelledby="tabs-beranda-tab">
                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Banner Beranda</h2>
                                <p>Gunakan gambar landscape agar hero beranda tetap rapi di desktop dan mobile.</p>
                            </div>

                            <div class="settings-upload-grid">
                                <div class="settings-field">
                                    <label for="hero_image">Banner Utama 1</label>
                                    <div class="settings-image-preview">
                                        @if(isset($settings['hero_image']) && $settings['hero_image'])
                                            <img src="{{ asset('storage/' . $settings['hero_image']) }}" alt="Hero 1">
                                        @else
                                            <i class="fas fa-image"></i>
                                        @endif
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="hero_image" id="hero_image" accept="image/*">
                                        <label class="custom-file-label text-truncate" for="hero_image">Pilih Banner 1</label>
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="hero_image_2">Banner Slide 2</label>
                                    <div class="settings-image-preview">
                                        @if(isset($settings['hero_image_2']) && $settings['hero_image_2'])
                                            <img src="{{ asset('storage/' . $settings['hero_image_2']) }}" alt="Hero 2">
                                        @else
                                            <i class="fas fa-image"></i>
                                        @endif
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="hero_image_2" id="hero_image_2" accept="image/*">
                                        <label class="custom-file-label text-truncate" for="hero_image_2">Pilih Banner 2</label>
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="hero_image_3">Banner Slide 3</label>
                                    <div class="settings-image-preview">
                                        @if(isset($settings['hero_image_3']) && $settings['hero_image_3'])
                                            <img src="{{ asset('storage/' . $settings['hero_image_3']) }}" alt="Hero 3">
                                        @else
                                            <i class="fas fa-image"></i>
                                        @endif
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="hero_image_3" id="hero_image_3" accept="image/*">
                                        <label class="custom-file-label text-truncate" for="hero_image_3">Pilih Banner 3</label>
                                    </div>
                                </div>
                            </div>

                            <p class="settings-note">Resolusi disarankan minimum 1920x800px. Jika lebih dari satu gambar diunggah, beranda otomatis memakai carousel.</p>
                        </div>

                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Konten Ringkas Beranda</h2>
                                <p>Teks pendukung untuk profil singkat dan sambutan kepala sekolah.</p>
                            </div>

                            <div class="settings-grid">
                                <div class="settings-field">
                                    <label for="beranda_profil_judul">Slogan Generasi Beranda</label>
                                    <input type="text" class="form-control" id="beranda_profil_judul" name="beranda_profil_judul" value="{{ $settings['beranda_profil_judul'] ?? '' }}" placeholder="Islami & Berprestasi">
                                </div>

                                <div class="settings-field">
                                    <label for="kepsek_nama">Nama Lengkap Kepala Sekolah</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="kepsek_nama" name="kepsek_nama" value="{{ $settings['kepsek_nama'] ?? '' }}" placeholder="Drs. Fulan, M.Pd...">
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="beranda_profil_teks">Deskripsi Ringkas Sekolah</label>
                                    <textarea class="form-control" id="beranda_profil_teks" name="beranda_profil_teks" rows="4">{{ $settings['beranda_profil_teks'] ?? '' }}</textarea>
                                </div>

                                <div class="settings-field">
                                    <label for="kepsek_sambutan">Teks Sambutan Kepala Sekolah</label>
                                    <textarea class="form-control" id="kepsek_sambutan" name="kepsek_sambutan" rows="4">{{ $settings['kepsek_sambutan'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tabs-kontak" role="tabpanel" aria-labelledby="tabs-kontak-tab">
                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Kontak Sekolah</h2>
                                <p>Informasi kontak yang ditampilkan pada footer dan area kontak publik.</p>
                            </div>

                            <div class="settings-grid">
                                <div class="settings-field">
                                    <label for="telepon">Nomor Telepon / WhatsApp</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone text-success"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="telepon" name="telepon" value="{{ $settings['telepon'] ?? '' }}" placeholder="(0274) 585755">
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="email">Email Kantor</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope text-danger"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $settings['email'] ?? '' }}" placeholder="info@sekolah.sch.id">
                                    </div>
                                </div>

                                <div class="settings-field settings-field-full">
                                    <label for="alamat">Alamat Gedung Lengkap</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ $settings['alamat'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Tautan Sosial Media</h2>
                                <p>Isi hanya platform yang aktif digunakan sekolah.</p>
                            </div>

                            <div class="settings-grid settings-grid-compact">
                                <div class="settings-field">
                                    <label for="facebook">Facebook</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-facebook text-primary"></i></span>
                                        </div>
                                        <input type="url" class="form-control" id="facebook" name="facebook" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="instagram">Instagram</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-instagram text-fuchsia"></i></span>
                                        </div>
                                        <input type="url" class="form-control" id="instagram" name="instagram" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="youtube">YouTube</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-youtube text-danger"></i></span>
                                        </div>
                                        <input type="url" class="form-control" id="youtube" name="youtube" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/...">
                                    </div>
                                </div>

                                <div class="settings-field">
                                    <label for="tiktok">TikTok</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-tiktok text-dark"></i></span>
                                        </div>
                                        <input type="url" class="form-control" id="tiktok" name="tiktok" value="{{ $settings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batalkan</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
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
