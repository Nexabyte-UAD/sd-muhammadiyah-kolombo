{{--
    Halaman Sunting Profil Sekolah (admin/profil-sekolah/edit.blade.php)
    Menyediakan formulir pembaruan halaman profil statis sekolah (Tentang Sekolah, Kata Sambutan,
    Visi & Misi, dan Akreditasi). Menampilkan field isian khusus (seperti hanya upload sertifikat
    untuk tipe Akreditasi) atau field teks panjang (konten) dengan upload cover gambar pendukung.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', $profil->judul)
@section('page_kicker', 'Konten website · Profil Sekolah')
@section('page_title', $profil->judul)
@section('page_description', 'Kelola informasi profil sekolah untuk ditampilkan pada website publik.')

@section('content')
    @if($errors->any())
        <div class="alert alert-danger m-3">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.profil-sekolah.updateType', $type) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Form Profil Sekolah</h2>
            <p>Perubahan akan langsung ditampilkan di halaman publik setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            <div class="form-grid">
                @if($type === 'akreditasi')
                    <!-- Khusus Akreditasi: Hanya Upload Gambar Sertifikat -->
                    <input type="hidden" name="judul" value="{{ old('judul', $profil->judul ?: 'Sertifikat Akreditasi') }}">
                    <input type="hidden" name="konten" value="{{ old('konten', $profil->konten ?: '-') }}">

                    <div class="form-field form-field-full">
                        <label for="gambar" class="form-label">Gambar Sertifikat <span>*</span></label>

                        <!-- Instant Image Preview Box -->
                        <div class="current-image mb-2" id="image-preview-box" style="display: none;">
                            <div style="max-width: 320px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                                <img src="#" id="image-preview-element" alt="Pratinjau Sertifikat" style="width: 100%; height: auto; display: block;">
                            </div>
                            <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">Pratinjau gambar baru (belum disimpan).</small>
                        </div>

                        <input type="file" name="gambar" id="gambar" class="form-control-admin form-file @error('gambar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" {{ $profil->gambar ? '' : 'required' }}>
                        <div class="form-help">Format: JPG, PNG, WEBP. Maksimal 2MB. {{ $profil->gambar ? 'Mengunggah gambar baru akan menimpa sertifikat lama.' : 'Wajib diunggah.' }}</div>
                        @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                @else
                    <!-- Tipe Umum (Visi Misi, Sejarah, dsb.) -->
                    <div class="form-field form-field-full">
                        <label for="judul" class="form-label">{{ $type === 'sambutan' ? 'Nama Kepala Sekolah' : 'Judul Halaman' }} <span>*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul', $profil->judul) }}" required placeholder="{{ $type === 'sambutan' ? 'Contoh: Drs. H. Ahmad Dahlan, M.Pd.' : '' }}">
                        @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    @if($type === 'sambutan')
                        <div class="form-field form-field-full">
                            <label for="sub_judul" class="form-label">Jabatan / Sub Judul Kepala Sekolah</label>
                            <input type="text" name="sub_judul" id="sub_judul" class="form-control-admin @error('sub_judul') is-invalid @enderror" value="{{ old('sub_judul', $sambutanData['sub_judul'] ?? 'Kepala Sekolah SD Muhammadiyah Komplek Kolombo') }}" placeholder="Contoh: Kepala Sekolah SD Muhammadiyah Komplek Kolombo">
                            <div class="form-help">Jabatan atau keterangan yang akan ditampilkan tepat di bawah nama Kepala Sekolah pada halaman publik.</div>
                            @error('sub_judul')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if($type === 'visi_misi')
                        <div class="form-field form-field-full">
                            <label for="visi" class="form-label">Visi Sekolah <span>*</span></label>
                            <textarea name="visi" id="visi" class="form-control-admin @error('visi') is-invalid @enderror" rows="5" placeholder="Tuliskan visi sekolah..." required style="line-height: 1.6;">{{ old('visi', $visiMisi['visi'] ?? '') }}</textarea>
                            @error('visi')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-field form-field-full">
                            <label for="misi" class="form-label">Misi Sekolah <span>*</span></label>
                            <textarea name="misi" id="misi" class="form-control-admin @error('misi') is-invalid @enderror" rows="10" placeholder="Tulis satu poin misi pada setiap baris..." required style="line-height: 1.6;">{{ old('misi', implode("\n", $visiMisi['misi'] ?? [])) }}</textarea>
                            <div class="form-help">Gunakan satu baris untuk setiap poin misi. Nomor urut akan dibuat otomatis pada halaman publik.</div>
                            @error('misi')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    @elseif($type === 'spmb')
                        <div class="form-field form-field-full">
                            <div class="p-3 border rounded-3 mb-2" style="background-color: #f0fdf4; border-color: #bbf7d0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <x-admin-icon name="check-circle" size="20" class="text-success"/>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 0.9rem;">Status SPMB Otomatis Terhubung</strong>
                                        <span class="text-secondary small">Status Pendaftaran (<strong>{{ $spmbData['status_pendaftaran'] }}</strong>) & Tahun Ajaran Aktif (<strong>{{ $spmbData['tahun_ajaran'] }}</strong>) dihitung secara **otomatis** dari status tabel Kuota Pendaftaran di bawah.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-field form-field-full">
                            <label for="nomor_wa" class="form-label">Nomor WhatsApp Panitia PPDB <span>*</span></label>
                            <input type="text" name="nomor_wa" id="nomor_wa" class="form-control-admin @error('nomor_wa') is-invalid @enderror" value="{{ old('nomor_wa', $spmbData['nomor_wa'] ?? '6281234567890') }}" placeholder="Contoh: 6281234567890" required>
                            <div class="form-help">Gunakan format internasional diawali 62 (contoh: 6281234567890).</div>
                            @error('nomor_wa')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-field form-field-full">
                            <label for="pengantar" class="form-label">Pengantar & Informasi Umum SPMB <span>*</span></label>
                            <textarea name="pengantar" id="pengantar" class="form-control-admin @error('pengantar') is-invalid @enderror" rows="4" placeholder="Tuliskan pengantar ucapan selamat datang SPMB..." required style="line-height: 1.6;">{{ old('pengantar', $spmbData['pengantar'] ?? '') }}</textarea>
                            @error('pengantar')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-field form-field-full">
                            <label class="form-label">Persyaratan Umum & Usia (Daftar Poin Interaktif)</label>
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered align-middle mb-2" id="syarat-umum-table" style="background: #fff; border-radius: 8px; overflow: hidden;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 85%;">Poin Persyaratan Umum</th>
                                            <th style="width: 15%; text-align: center;">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="syarat-umum-tbody">
                                        @php
                                            $syaratUmumItems = old('persyaratan_umum_items', $spmbData['persyaratan_umum_items'] ?? []);
                                        @endphp
                                        @forelse($syaratUmumItems as $uIdx => $uItem)
                                            <tr>
                                                <td>
                                                    <input type="text" name="persyaratan_umum_items[]" class="form-control-admin" value="{{ $uItem }}" placeholder="Contoh: Calon siswa berusia minimal 6 tahun pada 1 Juli." required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-syarat-umum-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td>
                                                    <input type="text" name="persyaratan_umum_items[]" class="form-control-admin" value="Calon siswa berusia minimal 6 tahun pada tanggal 1 Juli tahun berjalan." placeholder="Contoh: Calon siswa berusia minimal 6 tahun pada 1 Juli." required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-syarat-umum-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-admin btn-admin-secondary btn-sm" id="add-syarat-umum-btn" style="width: auto; padding: 0.4rem 1rem;">
                                + Tambah Poin Persyaratan
                            </button>
                        </div>

                        <div class="form-field form-field-full">
                            <label class="form-label">Dokumen Persyaratan / Berkas (Daftar Berkas Interaktif)</label>
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered align-middle mb-2" id="syarat-berkas-table" style="background: #fff; border-radius: 8px; overflow: hidden;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 85%;">Nama Dokumen / Berkas Wajib</th>
                                            <th style="width: 15%; text-align: center;">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="syarat-berkas-tbody">
                                        @php
                                            $syaratBerkasItems = old('persyaratan_berkas_items', $spmbData['persyaratan_berkas_items'] ?? []);
                                        @endphp
                                        @forelse($syaratBerkasItems as $bIdx => $bItem)
                                            <tr>
                                                <td>
                                                    <input type="text" name="persyaratan_berkas_items[]" class="form-control-admin" value="{{ $bItem }}" placeholder="Contoh: Fotokopi Akta Kelahiran (2 lembar)" required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-syarat-berkas-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td>
                                                    <input type="text" name="persyaratan_berkas_items[]" class="form-control-admin" value="Fotokopi Akta Kelahiran (2 lembar)" placeholder="Contoh: Fotokopi Akta Kelahiran (2 lembar)" required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-syarat-berkas-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-admin btn-admin-secondary btn-sm" id="add-syarat-berkas-btn" style="width: auto; padding: 0.4rem 1rem;">
                                + Tambah Dokumen Berkas
                            </button>
                        </div>

                        <div class="form-field form-field-full">
                            <label class="form-label">Alur & Langkah Pendaftaran (Daftar Tahapan Interaktif)</label>
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered align-middle mb-2" id="alur-table" style="background: #fff; border-radius: 8px; overflow: hidden;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 35%;">Judul Langkah</th>
                                            <th style="width: 50%;">Penjelasan / Detail Langkah</th>
                                            <th style="width: 15%; text-align: center;">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="alur-tbody">
                                        @php
                                            $alurItems = old('alur_pendaftaran_items', $spmbData['alur_pendaftaran_items'] ?? []);
                                        @endphp
                                        @forelse($alurItems as $aIdx => $aItem)
                                            <tr>
                                                <td>
                                                    <input type="text" name="alur_pendaftaran_items[{{ $aIdx }}][judul]" class="form-control-admin" value="{{ $aItem['judul'] ?? '' }}" placeholder="Contoh: Pengisian Formulir" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="alur_pendaftaran_items[{{ $aIdx }}][deskripsi]" class="form-control-admin" value="{{ $aItem['deskripsi'] ?? '' }}" placeholder="Contoh: Orang tua/wali murid mengisi formulir pendaftaran..." required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-alur-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td>
                                                    <input type="text" name="alur_pendaftaran_items[0][judul]" class="form-control-admin" value="Pengisian Formulir" placeholder="Contoh: Pengisian Formulir" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="alur_pendaftaran_items[0][deskripsi]" class="form-control-admin" value="Mengisi formulir pendaftaran di sekolah atau via WhatsApp." placeholder="Contoh: Orang tua/wali murid mengisi formulir pendaftaran..." required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-alur-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-admin btn-admin-secondary btn-sm" id="add-alur-btn" style="width: auto; padding: 0.4rem 1rem;">
                                + Tambah Langkah Pendaftaran
                            </button>
                        </div>

                        <div class="form-field form-field-full">
                            <label class="form-label">Daftar Kuota Pendaftaran per Tahun Ajaran (Interaktif)</label>
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered align-middle mb-2" id="kuota-repeater-table" style="background: #fff; border-radius: 8px; overflow: hidden;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 55%;">Tahun Ajaran</th>
                                            <th style="width: 35%;">Status Kuota</th>
                                            <th style="width: 10%; text-align: center;">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kuota-repeater-tbody">
                                        @php
                                            $kuotaList = old('kuota_items', $spmbData['kuota_items'] ?? []);
                                        @endphp
                                        @forelse($kuotaList as $idx => $kItem)
                                            <tr>
                                                <td>
                                                    <input type="text" name="kuota_items[{{ $idx }}][tahun_ajaran]" class="form-control-admin" value="{{ $kItem['tahun_ajaran'] ?? '' }}" placeholder="Contoh: Tahun Ajaran 2026/2027" required>
                                                </td>
                                                <td>
                                                    <select name="kuota_items[{{ $idx }}][status]" class="form-control-admin">
                                                        <option value="Masih Ada Kuota" {{ ($kItem['status'] ?? '') === 'Masih Ada Kuota' ? 'selected' : '' }}>Masih Ada Kuota (Label Hijau)</option>
                                                        <option value="Ditutup" {{ ($kItem['status'] ?? '') === 'Ditutup' ? 'selected' : '' }}>Ditutup (Label Merah)</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-kuota-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td>
                                                    <input type="text" name="kuota_items[0][tahun_ajaran]" class="form-control-admin" value="Tahun Ajaran 2026/2027" placeholder="Contoh: Tahun Ajaran 2026/2027" required>
                                                </td>
                                                <td>
                                                    <select name="kuota_items[0][status]" class="form-control-admin">
                                                        <option value="Masih Ada Kuota" selected>Masih Ada Kuota (Label Hijau)</option>
                                                        <option value="Ditutup">Ditutup (Label Merah)</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-kuota-row" title="Hapus Baris">&times;</button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-admin btn-admin-secondary btn-sm" id="add-kuota-row-btn" style="width: auto; padding: 0.4rem 1rem;">
                                + Tambah Baris Tahun Ajaran
                            </button>
                            <div class="form-help mt-1">Klik tombol <strong>+ Tambah Baris Tahun Ajaran</strong> untuk menambahkan baris baru, lalu tentukan status kuotanya.</div>
                        </div>
                    @else
                        <div class="form-field form-field-full">
                            <label for="konten" class="form-label">Isi Konten / Penjelasan <span>*</span></label>
                            <textarea name="konten" id="konten" class="form-control-admin @error('konten') is-invalid @enderror" rows="12" placeholder="Ketik isi dari halaman ini..." required style="line-height: 1.6;">{{ old('konten', $type === 'sambutan' ? ($sambutanData['konten'] ?? '') : $profil->konten) }}</textarea>
                            <div class="form-help">Gunakan tombol <code>Enter</code> pada keyboard untuk memisahkan paragraf satu dengan yang lainnya.</div>
                            @error('konten')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if(!in_array($type, ['visi_misi', 'spmb'], true))
                        <div class="form-field form-field-full">
                            <label for="gambar" class="form-label">Gambar Header / Pendukung (Opsional)</label>
                            
                            <!-- Instant Image Preview Box -->
                            <div class="current-image mb-2" id="image-preview-box" style="display: none;">
                                <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                                    <img src="#" id="image-preview-element" alt="Pratinjau Gambar" style="width: 100%; height: auto; display: block;">
                                </div>
                                <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">Pratinjau gambar baru (belum disimpan).</small>
                            </div>

                            <input type="file" name="gambar" id="gambar" class="form-control-admin form-file @error('gambar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-help">Format: JPG, PNG, WEBP. Maksimal 2MB. Mengunggah gambar baru akan otomatis menimpa gambar lama.</div>
                            @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    @endif
                @endif
            </div>
        </div>
        <div class="form-card-footer">
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 320px;
        }
    </style>
@endpush

@push('scripts')
    @if(!in_array($type, ['akreditasi', 'visi_misi', 'spmb'], true))
        <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.1/build/ckeditor.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const kontenEl = document.querySelector('#konten');
                if (kontenEl) {
                    // Initialize CKEditor 5
                    ClassicEditor
                        .create(kontenEl, {
                            toolbar: [
                                'heading', '|', 
                                'bold', 'italic', 'link', '|',
                                'bulletedList', 'numberedList', '|',
                                'blockQuote', 'undo', 'redo'
                            ],
                            heading: {
                                options: [
                                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                                ]
                            }
                        })
                        .then(editor => {
                            // Update textarea before form submission
                            editor.model.document.on('change:data', () => {
                                kontenEl.value = editor.getData();
                            });
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Instant Image Preview & Size Validation
            const gambarInput = document.getElementById('gambar');
            if (gambarInput) {
                gambarInput.addEventListener('change', function(event) {
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
                                if (previewHelp) previewHelp.textContent = 'Pratinjau gambar baru (belum disimpan).';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Helper function to setup dynamic repeaters
            function setupRepeater(tbodyId, addBtnId, removeClass, createRowFn) {
                const tbody = document.getElementById(tbodyId);
                const addBtn = document.getElementById(addBtnId);
                if (addBtn && tbody) {
                    addBtn.addEventListener('click', function () {
                        const count = tbody.querySelectorAll('tr').length;
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = createRowFn(count);
                        tbody.appendChild(newRow);
                    });
                    tbody.addEventListener('click', function (e) {
                        if (e.target.classList.contains(removeClass) || e.target.closest('.' + removeClass)) {
                            const tr = e.target.closest('tr');
                            if (tbody.querySelectorAll('tr').length > 1) {
                                tr.remove();
                            } else {
                                alert('Minimal harus ada 1 baris data.');
                            }
                        }
                    });
                }
            }

            // 1. Syarat Umum Repeater
            setupRepeater('syarat-umum-tbody', 'add-syarat-umum-btn', 'remove-syarat-umum-row', function() {
                return `
                    <td>
                        <input type="text" name="persyaratan_umum_items[]" class="form-control-admin" placeholder="Contoh: Calon siswa berusia minimal 6 tahun pada 1 Juli." required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-syarat-umum-row" title="Hapus Baris">&times;</button>
                    </td>
                `;
            });

            // 2. Syarat Berkas Repeater
            setupRepeater('syarat-berkas-tbody', 'add-syarat-berkas-btn', 'remove-syarat-berkas-row', function() {
                return `
                    <td>
                        <input type="text" name="persyaratan_berkas_items[]" class="form-control-admin" placeholder="Contoh: Fotokopi Akta Kelahiran (2 lembar)" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-syarat-berkas-row" title="Hapus Baris">&times;</button>
                    </td>
                `;
            });

            // 3. Alur Pendaftaran Repeater
            setupRepeater('alur-tbody', 'add-alur-btn', 'remove-alur-row', function(index) {
                return `
                    <td>
                        <input type="text" name="alur_pendaftaran_items[${index}][judul]" class="form-control-admin" placeholder="Contoh: Langkah Baru" required>
                    </td>
                    <td>
                        <input type="text" name="alur_pendaftaran_items[${index}][deskripsi]" class="form-control-admin" placeholder="Contoh: Penjelasan detail langkah..." required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-alur-row" title="Hapus Baris">&times;</button>
                    </td>
                `;
            });

            // 4. Kuota Repeater Table
            setupRepeater('kuota-repeater-tbody', 'add-kuota-row-btn', 'remove-kuota-row', function(index) {
                return `
                    <td>
                        <input type="text" name="kuota_items[${index}][tahun_ajaran]" class="form-control-admin" placeholder="Contoh: Tahun Ajaran ${2026 + index}/${2027 + index}" required>
                    </td>
                    <td>
                        <select name="kuota_items[${index}][status]" class="form-control-admin">
                            <option value="Masih Ada Kuota" selected>Masih Ada Kuota (Label Hijau)</option>
                            <option value="Ditutup">Ditutup (Label Merah)</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-kuota-row" title="Hapus Baris">&times;</button>
                    </td>
                `;
            });
        });
    </script>
@endpush
