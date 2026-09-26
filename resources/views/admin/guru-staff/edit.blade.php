{{--
    Halaman Sunting Pegawai Guru/Staf (admin/guru-staff/edit.blade.php)
    Menyediakan formulir pembaruan data guru/staf kependidikan terdaftar,
    lengkap dengan pratinjau gambar foto profil saat ini atau gambar baru yang dipilih.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Edit Pegawai')
@section('page_kicker', 'Akademik · Pegawai')
@section('page_title', 'Edit ' . ucfirst($guru->tipe))
@section('page_description', 'Perbarui data guru atau staf kependidikan.')

@section('page_actions')
    <a href="{{ route('admin.guru-staff.index', ['tipe' => $guru->tipe]) }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.guru-staff.update', $guru->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Informasi Profil {{ ucfirst($guru->tipe) }}</h2>
            <p>Perubahan akan diterapkan setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="nama" class="form-label">Nama Lengkap <span>*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control-admin @error('nama') is-invalid @enderror" value="{{ old('nama', $guru->nama) }}" required placeholder="Contoh: Ahmad Dahlan, S.Pd">
                    @error('nama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span>*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control-admin @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">Pilih jenis kelamin</option>
                        @foreach($jenisKelamin as $value => $label)
                            <option value="{{ $value }}" @selected(old('jenis_kelamin', $guru->jenis_kelamin) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_kelamin')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" name="nip" id="nip" class="form-control-admin @error('nip') is-invalid @enderror" value="{{ old('nip', $guru->nip) }}" placeholder="Nomor Induk Pegawai (Opsional)">
                    @error('nip')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="status_kepegawaian" class="form-label">Status Kepegawaian</label>
                    <select name="status_kepegawaian" id="status_kepegawaian" class="form-control-admin @error('status_kepegawaian') is-invalid @enderror">
                        <option value="">Pilih status kepegawaian (Opsional)</option>
                        @foreach($statusKepegawaian as $value => $label)
                            <option value="{{ $value }}" @selected(old('status_kepegawaian', $guru->status_kepegawaian) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_kepegawaian')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <input type="hidden" name="tipe" value="{{ $guru->tipe }}">

                <div class="form-field">
                    <label for="jabatan" class="form-label">Jabatan Pokok <span>*</span></label>
                    <input type="text" name="jabatan" id="jabatan" class="form-control-admin @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $guru->jabatan) }}" required placeholder="Contoh: Guru Kelas, Wali Kelas, Bendahara">
                    @error('jabatan')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="bidang_tugas" class="form-label">Bidang Tugas</label>
                    <input type="text" name="bidang_tugas" id="bidang_tugas" class="form-control-admin @error('bidang_tugas') is-invalid @enderror" value="{{ old('bidang_tugas', $guru->bidang_tugas) }}" placeholder="Contoh: Guru Kelas, Tata Usaha">
                    <div class="form-help">Dapat dikosongkan jika tidak memiliki bidang khusus.</div>
                    @error('bidang_tugas')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir <span>*</span></label>
                    <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control-admin @error('pendidikan_terakhir') is-invalid @enderror" required>
                        <option value="">Pilih pendidikan terakhir</option>
                        @foreach($pendidikanTerakhir as $value => $label)
                            <option value="{{ $value }}" @selected(old('pendidikan_terakhir', $guru->pendidikan_terakhir) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('pendidikan_terakhir')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="agama" class="form-label">Agama <span>*</span></label>
                    <select name="agama" id="agama" class="form-control-admin @error('agama') is-invalid @enderror" required>
                        <option value="">Pilih agama</option>
                        @foreach($daftarAgama as $value => $label)
                            <option value="{{ $value }}" @selected(old('agama', $guru->agama) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('agama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label class="form-label">Foto Profil Saat Ini / Pratinjau Baru</label>
                    
                    <!-- Instant Image Preview Box -->
                    <div class="current-image mb-2" id="image-preview-box">
                        <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            @if($guru->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($guru->foto))
                                <img src="{{ asset('storage/' . $guru->foto) }}" id="image-preview-element" alt="{{ $guru->nama }}" style="width: 100%; height: auto; display: block;">
                            @else
                                <img src="{{ asset('assets/images/no-image-available.jpg') }}" id="image-preview-element" alt="Default" style="width: 100%; height: auto; display: block;">
                            @endif
                        </div>
                        <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">
                            {{ $guru->foto ? 'Foto tersimpan saat ini.' : 'Belum ada foto.' }}
                        </small>
                    </div>

                    <label for="foto" class="form-label">Ganti Foto Profil Baru (Opsional)</label>
                    <input type="file" name="foto" id="foto"
                           class="form-control-admin form-file @error('foto') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Biarkan kosong jika tidak ingin mengubah foto profil. Maksimal 2MB.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.guru-staff.index', ['tipe' => $guru->tipe]) }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
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
                            const previewHelp = document.getElementById('image-preview-help');

                            if (previewEl && previewBox) {
                                previewEl.src = e.target.result;
                                previewBox.style.display = 'block';
                                if (previewHelp) previewHelp.textContent = 'Pratinjau foto baru (belum disimpan).';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
