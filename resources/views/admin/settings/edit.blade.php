{{--
    Halaman Pengaturan Sistem (admin/settings/edit.blade.php)
    Menyediakan antar muka penyesuaian key-value konfigurasi dinamis (nama sekolah, kontak,
    sosial media, dan gambar banner slider depan), terbagi atas tab navigasi Identitas, Beranda,
    dan Kontak.
--}}
@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page_kicker', 'Administrasi')
@section('page_title', 'Pengaturan Sistem')
@section('page_description', 'Kelola identitas sekolah, tampilan beranda, kontak, dan tautan publik website.')

@section('content')
    <div class="settings-layout">
        <x-admin-usage-guide
            description="Petunjuk pengelolaan konfigurasi sistem, identitas sekolah, dan sosial media."
            :items="[
                'Gunakan tab Identitas Sekolah untuk mengubah nama resmi, logo, dan nomor telepon utama.',
                'Gunakan tab Pengaturan Beranda untuk memperbarui foto banner geser (carousel) dan kata sambutan singkat kepala sekolah.',
                'Gunakan tab Kontak & Footer untuk memperbarui alamat fisik sekolah dan tautan jejaring sosial media resmi.',
                'Menyimpan perubahan di halaman ini akan langsung memperbarui identitas di seluruh halaman publik.',
            ]"
        />

        @if($errors->any())
            <div class="alert alert-danger m-3">
                <strong>Terjadi kesalahan.</strong>
                <ul class="mb-0 mt-2 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form" onsubmit="return confirm('Perubahan ini akan langsung memengaruhi identitas sekolah dan tampilan halaman depan website. Simpan perubahan?')">
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
                    
                    <!-- Tab Identitas -->
                    <div class="tab-pane fade show active" id="tabs-identitas" role="tabpanel" aria-labelledby="tabs-identitas-tab">
                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Identitas Institusi</h2>
                                <p>Informasi dasar yang tampil pada judul halaman, header, dan footer website.</p>
                            </div>

                            <div class="settings-grid">
                                <div class="settings-field settings-field-wide">
                                    <label for="nama_sekolah" class="form-label">Nama Institusi / Sekolah <span>*</span></label>
                                    <input type="text" class="form-control-admin" id="nama_sekolah" name="nama_sekolah" value="{{ $settings['nama_sekolah'] ?? '' }}" required>
                                    <div class="form-help">Akan ditampilkan di tab judul, header, dan atribusi footer bawah.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Beranda -->
                    <div class="tab-pane fade" id="tabs-beranda" role="tabpanel" aria-labelledby="tabs-beranda-tab">
                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Banner Beranda</h2>
                                <p>Gunakan gambar landscape agar hero beranda tetap rapi di desktop dan mobile.</p>
                            </div>

                            <div class="settings-upload-grid">
                                <!-- Banner 1 -->
                                <div class="settings-field">
                                    <label for="hero_image" class="form-label">Banner Utama 1</label>
                                    <div class="settings-image-preview" id="preview-box-hero_image">
                                        @if(isset($settings['hero_image']) && $settings['hero_image'])
                                            <img src="{{ asset('storage/' . $settings['hero_image']) }}" id="preview-el-hero_image" alt="Hero 1">
                                        @else
                                            <x-admin-icon name="image" size="42" id="preview-icon-hero_image"/>
                                            <img src="#" id="preview-el-hero_image" alt="Hero 1" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                    <input type="file" class="form-control-admin form-file" name="hero_image" id="hero_image" accept="image/*">
                                </div>

                                <!-- Banner 2 -->
                                <div class="settings-field">
                                    <label for="hero_image_2" class="form-label">Banner Slide 2</label>
                                    <div class="settings-image-preview" id="preview-box-hero_image_2">
                                        @if(isset($settings['hero_image_2']) && $settings['hero_image_2'])
                                            <img src="{{ asset('storage/' . $settings['hero_image_2']) }}" id="preview-el-hero_image_2" alt="Hero 2">
                                        @else
                                            <x-admin-icon name="image" size="42" id="preview-icon-hero_image_2"/>
                                            <img src="#" id="preview-el-hero_image_2" alt="Hero 2" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                    <input type="file" class="form-control-admin form-file" name="hero_image_2" id="hero_image_2" accept="image/*">
                                </div>

                                <!-- Banner 3 -->
                                <div class="settings-field">
                                    <label for="hero_image_3" class="form-label">Banner Slide 3</label>
                                    <div class="settings-image-preview" id="preview-box-hero_image_3">
                                        @if(isset($settings['hero_image_3']) && $settings['hero_image_3'])
                                            <img src="{{ asset('storage/' . $settings['hero_image_3']) }}" id="preview-el-hero_image_3" alt="Hero 3">
                                        @else
                                            <x-admin-icon name="image" size="42" id="preview-icon-hero_image_3"/>
                                            <img src="#" id="preview-el-hero_image_3" alt="Hero 3" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                    <input type="file" class="form-control-admin form-file" name="hero_image_3" id="hero_image_3" accept="image/*">
                                </div>
                            </div>

                            <div class="form-help mt-2">Resolusi disarankan minimum 1920x800px. Jika lebih dari satu gambar diunggah, beranda otomatis memakai carousel.</div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Konten Ringkas Beranda</h2>
                                <p>Teks pendukung untuk profil singkat dan sambutan kepala sekolah.</p>
                            </div>

                            <div class="settings-grid">
                                <div class="settings-field">
                                    <label for="beranda_profil_judul" class="form-label">Slogan Generasi Beranda</label>
                                    <input type="text" class="form-control-admin" id="beranda_profil_judul" name="beranda_profil_judul" value="{{ $settings['beranda_profil_judul'] ?? '' }}" placeholder="Islami & Berprestasi">
                                </div>

                                <div class="settings-field">
                                    <label for="kepsek_nama" class="form-label">Nama Lengkap Kepala Sekolah</label>
                                    <input type="text" class="form-control-admin" id="kepsek_nama" name="kepsek_nama" value="{{ $settings['kepsek_nama'] ?? '' }}" placeholder="Drs. Fulan, M.Pd...">
                                </div>

                                <div class="settings-field">
                                    <label for="beranda_profil_teks" class="form-label">Deskripsi Ringkas Sekolah</label>
                                    <textarea class="form-control-admin" id="beranda_profil_teks" name="beranda_profil_teks" rows="4">{{ $settings['beranda_profil_teks'] ?? '' }}</textarea>
                                </div>

                                <div class="settings-field">
                                    <label for="welcome_image" class="form-label">Gambar Selamat Datang</label>
                                    <div class="settings-image-preview" id="preview-box-welcome_image" style="width: 100%; height: 180px; background: #f1f5f9; border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                        @if(isset($settings['welcome_image']) && $settings['welcome_image'])
                                            <img src="{{ asset('storage/' . $settings['welcome_image']) }}" id="preview-el-welcome_image" alt="Selamat Datang" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <x-admin-icon name="image" size="42" id="preview-icon-welcome_image" style="color: #94a3b8;"/>
                                            <img src="#" id="preview-el-welcome_image" alt="Selamat Datang" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                    <input type="file" class="form-control-admin form-file" name="welcome_image" id="welcome_image" accept="image/*">
                                    <div class="form-help">Gambar default yang tampil di sebelah teks Selamat Datang beranda.</div>
                                </div>

                                <div class="settings-field">
                                    <label for="kepsek_sambutan" class="form-label">Teks Sambutan Kepala Sekolah</label>
                                    <textarea class="form-control-admin" id="kepsek_sambutan" name="kepsek_sambutan" rows="4">{{ $settings['kepsek_sambutan'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Kontak -->
                    <div class="tab-pane fade" id="tabs-kontak" role="tabpanel" aria-labelledby="tabs-kontak-tab">
                        <div class="settings-section">
                            <div class="settings-section-heading">
                                <h2>Kontak Sekolah</h2>
                                <p>Informasi kontak yang ditampilkan pada footer dan area kontak publik.</p>
                            </div>

                            <div class="settings-grid">
                                <div class="settings-field">
                                    <label for="telepon" class="form-label">Nomor Telepon / WhatsApp</label>
                                    <input type="text" class="form-control-admin" id="telepon" name="telepon" value="{{ $settings['telepon'] ?? '' }}" placeholder="(0274) 585755">
                                </div>

                                <div class="settings-field">
                                    <label for="email" class="form-label">Email Kantor</label>
                                    <input type="email" class="form-control-admin" id="email" name="email" value="{{ $settings['email'] ?? '' }}" placeholder="info@sekolah.sch.id">
                                </div>

                                <div class="settings-field settings-field-full">
                                    <label for="alamat" class="form-label">Alamat Gedung Lengkap</label>
                                    <textarea class="form-control-admin" id="alamat" name="alamat" rows="3">{{ $settings['alamat'] ?? '' }}</textarea>
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
                                    <label for="facebook" class="form-label">Facebook</label>
                                    <input type="url" class="form-control-admin" id="facebook" name="facebook" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
                                </div>

                                <div class="settings-field">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <input type="url" class="form-control-admin" id="instagram" name="instagram" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
                                </div>

                                <div class="settings-field">
                                    <label for="youtube" class="form-label">YouTube</label>
                                    <input type="url" class="form-control-admin" id="youtube" name="youtube" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/...">
                                </div>

                                <div class="settings-field">
                                    <label for="tiktok" class="form-label">TikTok</label>
                                    <input type="url" class="form-control-admin" id="tiktok" name="tiktok" value="{{ $settings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <a href="{{ route('dashboard') }}" class="btn-admin btn-admin-secondary">Batalkan</a>
                <button type="submit" class="btn-admin">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Function to handle image preview
            function setupImagePreview(inputId) {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            if (file.size > 2 * 1024 * 1024) {
                                alert('Ukuran file terlalu besar! Maksimal 2 MB.');
                                event.target.value = '';
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewEl = document.getElementById('preview-el-' + inputId);
                                const previewIcon = document.getElementById('preview-icon-' + inputId);
                                
                                if (previewEl) {
                                    previewEl.src = e.target.result;
                                    previewEl.style.display = 'block';
                                    if (previewIcon) {
                                        previewIcon.style.display = 'none';
                                    }
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }

            setupImagePreview('hero_image');
            setupImagePreview('hero_image_2');
            setupImagePreview('hero_image_3');
            setupImagePreview('welcome_image');
        });
    </script>
@endpush
