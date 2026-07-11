{{--
    Halaman Sunting Data Siswa (admin/siswa/edit.blade.php)
    Menyediakan formulir pembaruan biodata siswa terdaftar, riwayat ekstrakurikuler, kelas,
    serta integrasi riwayat pendidikan alumni jika status siswa tersebut diset ke alumni.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Edit Siswa')
@section('page_kicker', 'Akademik · Siswa')
@section('page_title', 'Edit Siswa')
@section('page_description', 'Perbarui biodata dan status siswa.')

@section('page_actions')
    <a href="{{ $siswa->status === 'alumni' ? route('admin.alumni.index') : route('admin.siswa.index', ['status' => $siswa->trashed() ? 'arsip' : $siswa->status]) }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert alert-danger m-3">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Perbarui Biodata: {{ $siswa->nama }}</h2>
            <p>Perubahan akan diterapkan setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            <x-auto-format-notice />
            <div class="row">
                <!-- Nama Lengkap -->
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Lengkap <span>*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control-admin @error('nama') is-invalid @enderror" value="{{ old('nama', $siswa->nama) }}" required placeholder="Masukkan nama lengkap siswa">
                    @error('nama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span>*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control-admin @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                    @error('jenis_kelamin')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- NIS -->
                <div class="col-md-6 mb-3">
                    <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" id="nis" class="form-control-admin @error('nis') is-invalid @enderror" value="{{ old('nis', $siswa->nis) }}" placeholder="Masukkan NIS (Opsional)">
                    @error('nis')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Agama -->
                <div class="col-md-6 mb-3">
                    <label for="agama" class="form-label">Agama <span>*</span></label>
                    <select name="agama" id="agama" class="form-control-admin @error('agama') is-invalid @enderror" required>
                        <option value="">-- Pilih Agama --</option>
                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                            <option value="{{ $agama }}" @selected(old('agama', $siswa->agama) === $agama)>{{ $agama }}</option>
                        @endforeach
                    </select>
                    @error('agama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Tempat Lahir -->
                <div class="col-md-6 mb-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control-admin @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" placeholder="Contoh: Sleman, Yogyakarta">
                    @error('tempat_lahir')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Tanggal Lahir -->
                <div class="col-md-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control-admin @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}">
                    @error('tanggal_lahir')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Alamat -->
                <div class="col-md-12 mb-3">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat" rows="2" class="form-control-admin @error('alamat') is-invalid @enderror" placeholder="Alamat rumah siswa...">{{ old('alamat', $siswa->alamat) }}</textarea>
                    @error('alamat')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="ekstrakurikuler_ids" class="form-label">Ekstrakurikuler</label>
                    <select name="ekstrakurikuler_ids[]" id="ekstrakurikuler_ids"
                            class="form-control-admin @error('ekstrakurikuler_ids') is-invalid @enderror"
                            multiple size="{{ min(max($daftarEkstrakurikuler->count(), 3), 6) }}">
                        @php($ekstrakurikulerTerpilih = old('ekstrakurikuler_ids', $siswa->ekstrakurikulers->pluck('id')->all()))
                        @foreach($daftarEkstrakurikuler as $ekstrakurikuler)
                            <option value="{{ $ekstrakurikuler->id }}"
                                @selected(in_array((string) $ekstrakurikuler->id, array_map('strval', $ekstrakurikulerTerpilih), true))>
                                {{ $ekstrakurikuler->nama }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Tahan Ctrl untuk memilih lebih dari satu.</div>
                    @error('ekstrakurikuler_ids.*')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mb-3">
                    <hr class="my-4">
                    <h5 class="mb-3 font-weight-bold">Status Akademik</h5>
                </div>

                <!-- Status -->
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control-admin @error('status') is-invalid @enderror" required>
                        <option value="aktif" {{ old('status', $siswa->status) == 'aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                        <option value="alumni" {{ old('status', $siswa->status) == 'alumni' ? 'selected' : '' }}>Alumni / Lulus</option>
                        <option value="keluar" {{ old('status', $siswa->status) == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                    @error('status')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Kelas (Hanya Aktif) -->
                <div class="col-md-4 mb-3" id="div-kelas">
                    <label for="kelas" class="form-label">Kelas <span>*</span></label>
                    <select name="kelas" id="kelas" class="form-control-admin @error('kelas') is-invalid @enderror">
                        <option value="" disabled>-- Pilih Kelas --</option>
                        @foreach($daftarKelas as $itemKelas)
                            <option value="{{ $itemKelas->tingkat }}" @selected(old('kelas', $siswa->kelas) === $itemKelas->tingkat)>
                                {{ $itemKelas->tingkat }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Tahun Masuk -->
                <div class="col-md-4 mb-3">
                    <label for="tahun_masuk" class="form-label">Tahun Masuk <span>*</span></label>
                    <input type="number" name="tahun_masuk" id="tahun_masuk" class="form-control-admin @error('tahun_masuk') is-invalid @enderror" value="{{ old('tahun_masuk', $siswa->tahun_masuk) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                    @error('tahun_masuk')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Tahun Lulus (Hanya Alumni) -->
                <div class="col-md-4 mb-3" id="div-tahun-lulus" style="display: none;">
                    <label for="tahun_lulus" class="form-label">Tahun Lulus <span>*</span></label>
                    <input type="number" name="tahun_lulus" id="tahun_lulus" class="form-control-admin @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $siswa->tahun_lulus ?? date('Y')) }}" min="2000" max="{{ date('Y') + 5 }}">
                    @error('tahun_lulus')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mb-3" id="div-profil-alumni" style="display: none;">
                    <hr class="my-4">
                    <h5 class="mb-1 font-weight-bold">Data Lanjutan Alumni</h5>
                    <p class="text-muted small mb-3">Isi sesuai kondisi alumni. Semua kolom bersifat opsional.</p>
                    @include('admin.siswa._riwayat_alumni')
                </div>

                @include('admin.siswa._data_keluar')

                <div class="col-md-12 mb-3">
                    <hr class="my-4">
                    <h5 class="mb-3 font-weight-bold">Unggahan & Pratinjau Foto</h5>
                </div>

                <!-- Foto -->
                <div class="col-md-6 mb-3">
                    <label for="foto" class="form-label">Foto Siswa</label>
                    
                    <div class="current-image" id="image-preview-box" style="display: {{ $siswa->foto ? 'flex' : 'none' }}">
                        <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : '#' }}" id="image-preview-element" alt="Pratinjau Foto">
                        <div>
                            <strong id="image-preview-title">{{ $siswa->foto ? 'Foto saat ini' : 'Pratinjau gambar baru' }}</strong>
                            <small id="image-preview-help">{{ $siswa->foto ? 'Pilih file baru jika ingin menggantinya.' : 'Gambar belum disimpan.' }}</small>
                        </div>
                    </div>

                    <input type="file" name="foto" id="foto" class="form-control-admin form-file @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                    <div class="form-help">Format: JPG, PNG. Ukuran maksimal: 2MB. Biarkan kosong jika tidak ingin mengubah foto.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-card-footer">
            <a href="{{ $siswa->status === 'alumni' ? route('admin.alumni.index') : route('admin.siswa.index', ['status' => $siswa->trashed() ? 'arsip' : $siswa->status]) }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Dynamic Field Toggle depending on Status
            const statusSelect = document.getElementById('status');
            const divKelas = document.getElementById('div-kelas');
            const kelasSelect = document.getElementById('kelas');
            const divTahunLulus = document.getElementById('div-tahun-lulus');
            const tahunLulusInput = document.getElementById('tahun_lulus');
            const divProfilAlumni = document.getElementById('div-profil-alumni');
            const divDataKeluar = document.getElementById('div-data-keluar');

            function toggleStatusFields() {
                if (!statusSelect) return;
                const status = statusSelect.value;
                if (status === 'aktif') {
                    if (divKelas) divKelas.style.display = 'block';
                    if (kelasSelect) kelasSelect.required = true;
                    if (divTahunLulus) divTahunLulus.style.display = 'none';
                    if (divProfilAlumni) divProfilAlumni.style.display = 'none';
                    if (tahunLulusInput) tahunLulusInput.required = false;
                    if (divDataKeluar) divDataKeluar.style.display = 'none';
                } else if (status === 'alumni') {
                    if (divKelas) divKelas.style.display = 'none';
                    if (kelasSelect) kelasSelect.required = false;
                    if (divTahunLulus) divTahunLulus.style.display = 'block';
                    if (divProfilAlumni) divProfilAlumni.style.display = 'block';
                    if (tahunLulusInput) tahunLulusInput.required = true;
                    if (divDataKeluar) divDataKeluar.style.display = 'none';
                } else {
                    if (divKelas) divKelas.style.display = 'none';
                    if (divTahunLulus) divTahunLulus.style.display = 'none';
                    if (divProfilAlumni) divProfilAlumni.style.display = 'none';
                    if (kelasSelect) kelasSelect.required = false;
                    if (tahunLulusInput) tahunLulusInput.required = false;
                    if (divDataKeluar) divDataKeluar.style.display = 'block';
                }
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', toggleStatusFields);
                toggleStatusFields(); // Run initially
            }

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
