@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Tambah Siswa Baru</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-default">
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
                <h3 class="card-title">Biodata & Status Siswa</h3>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <x-auto-format-notice />
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <div class="row">
                        <!-- Nama Lengkap -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap siswa">
                                @error('nama')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                </select>
                                @error('jenis_kelamin')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- NIS -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nis">NIS (Nomor Induk Siswa)</label>
                                <input type="text" name="nis" id="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" placeholder="Masukkan NIS (Opsional)">
                                @error('nis')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Agama -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agama">Agama <span class="text-danger">*</span></label>
                                <select name="agama" id="agama" class="form-control @error('agama') is-invalid @enderror" required>
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" @selected(old('agama') === $agama)>{{ $agama }}</option>
                                    @endforeach
                                </select>
                                @error('agama')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Sleman, Yogyakarta">
                                @error('tempat_lahir')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="alamat">Alamat Lengkap</label>
                                <textarea name="alamat" id="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat rumah siswa...">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ekstrakurikuler_ids">Ekstrakurikuler</label>
                                <select name="ekstrakurikuler_ids[]" id="ekstrakurikuler_ids"
                                        class="form-control @error('ekstrakurikuler_ids') is-invalid @enderror"
                                        multiple size="{{ min(max($daftarEkstrakurikuler->count(), 3), 6) }}">
                                    @foreach($daftarEkstrakurikuler as $ekstrakurikuler)
                                        <option value="{{ $ekstrakurikuler->id }}"
                                            @selected(in_array((string) $ekstrakurikuler->id, array_map('strval', old('ekstrakurikuler_ids', [])), true))>
                                            {{ $ekstrakurikuler->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Tahan Ctrl untuk memilih lebih dari satu.</small>
                                @error('ekstrakurikuler_ids.*')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr class="my-4">
                            <h5>Status Akademik</h5>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                                    <option value="alumni" {{ old('status') == 'alumni' ? 'selected' : '' }}>Alumni / Lulus</option>
                                    <option value="keluar" {{ old('status') == 'keluar' ? 'selected' : '' }}>Pindah / Keluar</option>
                                </select>
                                @error('status')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Kelas (Hanya Aktif) -->
                        <div class="col-md-4" id="div-kelas">
                            <div class="form-group">
                                <label for="kelas">Kelas <span class="text-danger">*</span></label>
                                <select name="kelas" id="kelas" class="form-control @error('kelas') is-invalid @enderror">
                                    <option value="" disabled @selected(!old('kelas'))>-- Pilih Kelas --</option>
                                    @foreach($daftarKelas as $itemKelas)
                                        <option value="{{ $itemKelas->tingkat }}" @selected(old('kelas') === $itemKelas->tingkat)>
                                            {{ $itemKelas->tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tahun Masuk -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tahun_masuk">Tahun Masuk <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_masuk" id="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror" value="{{ old('tahun_masuk', old('tahun_masuk', date('Y'))) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                                @error('tahun_masuk')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tahun Lulus (Hanya Alumni) -->
                        <div class="col-md-4" id="div-tahun-lulus" style="display: none;">
                            <div class="form-group">
                                <label for="tahun_lulus">Tahun Lulus <span class="text-danger">*</span></label>
                                <input type="number" name="tahun_lulus" id="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', old('tahun_lulus', date('Y'))) }}" min="2000" max="{{ date('Y') + 5 }}">
                                @error('tahun_lulus')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12" id="div-profil-alumni" style="display: none;">
                            <hr class="my-4">
                            <h5>Data Lanjutan Alumni</h5>
                            <p class="text-muted small">Isi sesuai kondisi alumni. Semua kolom bersifat opsional.</p>
                            @include('admin.siswa._riwayat_alumni')
                            <div class="row d-none">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="melanjutkan_sekolah_di">Melanjutkan Sekolah Di</label>
                                        <input disabled type="text" name="pendidikan_lama[0][institusi]" id="melanjutkan_sekolah_di"
                                               class="form-control" value="{{ old('pendidikan.0.institusi') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jenjang">Jenjang</label>
                                        <input disabled type="text" name="pendidikan_lama[0][jenjang]" id="jenjang"
                                               class="form-control" value="{{ old('pendidikan.0.jenjang') }}" placeholder="Contoh: SMP / MTs / SMA">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="program_studi">Program Studi</label>
                                        <input disabled type="text" name="pendidikan_lama[0][jurusan]" id="program_studi"
                                               class="form-control" value="{{ old('pendidikan.0.jurusan') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pekerjaan">Pekerjaan</label>
                                        <input disabled type="text" name="pekerjaan_lama[0][pekerjaan]" id="pekerjaan"
                                               class="form-control" value="{{ old('pekerjaan_alumni.0.pekerjaan') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bekerja_di_perusahaan">Bekerja di Perusahaan</label>
                                        <input disabled type="text" name="pekerjaan_lama[0][perusahaan]" id="bekerja_di_perusahaan"
                                               class="form-control" value="{{ old('pekerjaan_alumni.0.perusahaan') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('admin.siswa._data_keluar')

                        <div class="col-md-12">
                            <hr class="my-4">
                            <h5>Unggahan</h5>
                        </div>

                        <!-- Foto -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="foto">Foto Siswa</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" accept="image/jpeg,image/png,image/jpg">
                                    <label class="custom-file-label" for="foto">Pilih file foto</label>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG. Ukuran maksimal: 2MB.</small>
                                @error('foto')
                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $(function () {
        // Custom File Input
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Dynamic Field Toggle depending on Status
        function toggleStatusFields() {
            let status = $('#status').val();
            if (status === 'aktif') {
                $('#div-kelas').show();
                $('#kelas').attr('required', true);
                $('#div-tahun-lulus').hide();
                $('#div-profil-alumni').hide();
                $('#tahun_lulus').attr('required', false);
                $('#div-data-keluar').hide();
            } else if (status === 'alumni') {
                $('#div-kelas').hide();
                $('#kelas').attr('required', false);
                $('#div-tahun-lulus').show();
                $('#div-profil-alumni').show();
                $('#tahun_lulus').attr('required', true);
                $('#div-data-keluar').hide();
            } else {
                $('#div-kelas, #div-tahun-lulus, #div-profil-alumni').hide();
                $('#kelas, #tahun_lulus').attr('required', false);
                $('#div-data-keluar').show();
            }
        }

        $('#status').on('change', toggleStatusFields);
        toggleStatusFields(); // Run on page load
    });
</script>
@endpush
