{{--
    Halaman Tambah Pegawai Guru/Staf Baru (admin/guru-staff/create.blade.php)
    Menyediakan formulir pendaftaran data guru/staf kependidikan baru, lengkap dengan input nama,
    jenis kelamin, NIP, status kepegawaian, jabatan, pendidikan, agama, unggah foto profil,
    dan skrip pratinjau gambar instant (client-side JS).
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Tambah Pegawai')
@section('page_kicker', 'Akademik · Pegawai')
@section('page_title', 'Tambah ' . ucfirst($tipe))
@section('page_description', 'Tambahkan data guru atau staf kependidikan baru.')

@section('page_actions')
    <a href="{{ route('admin.guru-staff.index', ['tipe' => $tipe]) }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.guru-staff.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        <div class="form-card-header">
            <h2>Informasi Profil {{ ucfirst($tipe) }}</h2>
            <p>Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="form-card-body">
            <x-auto-format-notice />
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="nama" class="form-label">Nama Lengkap <span>*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control-admin @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Contoh: Ahmad Dahlan, S.Pd">
                    @error('nama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span>*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control-admin @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">Pilih jenis kelamin</option>
                        @foreach($jenisKelamin as $value => $label)
                            <option value="{{ $value }}" @selected(old('jenis_kelamin') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_kelamin')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" name="nip" id="nip" class="form-control-admin @error('nip') is-invalid @enderror" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai (Opsional)">
                    @error('nip')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="status_kepegawaian" class="form-label">Status Kepegawaian</label>
                    <select name="status_kepegawaian" id="status_kepegawaian" class="form-control-admin @error('status_kepegawaian') is-invalid @enderror">
                        <option value="">Pilih status kepegawaian (Opsional)</option>
                        @foreach($statusKepegawaian as $value => $label)
                            <option value="{{ $value }}" @selected(old('status_kepegawaian') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_kepegawaian')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <input type="hidden" name="tipe" value="{{ $tipe }}">

                <div class="form-field">
                    <label for="jabatan" class="form-label">Jabatan Pokok <span>*</span></label>
                    <input type="text" name="jabatan" id="jabatan" class="form-control-admin @error('jabatan') is-invalid @enderror" value="{{ old('jabatan') }}" required placeholder="Contoh: Guru Kelas, Wali Kelas, Bendahara">
                    @error('jabatan')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="bidang_tugas" class="form-label">Bidang Tugas</label>
                    <input type="text" name="bidang_tugas" id="bidang_tugas" class="form-control-admin @error('bidang_tugas') is-invalid @enderror" value="{{ old('bidang_tugas') }}" placeholder="Contoh: Guru Kelas, Tata Usaha">
                    <div class="form-help">Dapat dikosongkan jika tidak memiliki bidang khusus.</div>
                    @error('bidang_tugas')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir <span>*</span></label>
                    <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control-admin @error('pendidikan_terakhir') is-invalid @enderror" required>
                        <option value="">Pilih pendidikan terakhir</option>
                        @foreach($pendidikanTerakhir as $value => $label)
                            <option value="{{ $value }}" @selected(old('pendidikan_terakhir') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('pendidikan_terakhir')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="agama" class="form-label">Agama <span>*</span></label>
                    <select name="agama" id="agama" class="form-control-admin @error('agama') is-invalid @enderror" required>
                        <option value="">Pilih agama</option>
                        @foreach($daftarAgama as $value => $label)
                            <option value="{{ $value }}" @selected(old('agama') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('agama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="foto" class="form-label">Foto Profil</label>
                    
                    <div class="current-image" id="image-preview-box">
                        <span class="current-image-placeholder"><x-admin-icon name="person-circle" size="30"/></span>
                        <img src="#" id="image-preview-element" alt="Pratinjau Gambar">
                        <div>
                            <strong id="image-preview-title">Pratinjau gambar baru</strong>
                            <small id="image-preview-help">Belum ada gambar yang dipilih.</small>
                        </div>
                    </div>

                    <input type="file" name="foto" id="foto"
                           class="form-control-admin form-file @error('foto') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/gif">
                    <div class="form-help">JPG, PNG, atau GIF. Maksimal 2 MB.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.guru-staff.index', ['tipe' => $tipe]) }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Pegawai</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Instant Image Preview & Size Validation
            const fotoInput = document.getElementById('foto');
            if (fotoInput) {
                fotoInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file terlalu besar! Maksimal 2 MB.');
                            event.target.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewBox = document.getElementById('image-preview-box');
                            const previewEl = document.getElementById('image-preview-element');
                            const previewTitle = document.getElementById('image-preview-title');
                            const previewHelp = document.getElementById('image-preview-help');

                            if (previewEl && previewBox) {
                                previewEl.src = e.target.result;
                                previewBox.style.display = 'flex';
                                if (previewTitle) previewTitle.textContent = 'Pratinjau gambar baru';
                                if (previewHelp) previewHelp.textContent = 'Gambar terpilih (belum disimpan).';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
