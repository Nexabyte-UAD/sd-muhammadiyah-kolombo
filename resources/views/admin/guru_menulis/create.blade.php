@extends('layouts.admin')

@section('title', 'Tambah Artikel Guru Menulis')
@section('page_kicker', 'Guru Menulis')
@section('page_title', 'Tambah Artikel Baru')

@section('content')
    <form action="{{ route('admin.guru-menulis.store') }}" method="POST" enctype="multipart/form-data" class="admin-card">
        @csrf
        <div class="form-card-header">
            <h2>Tambah Artikel Guru Menulis</h2>
            <p>Publikasikan artikel, opini, atau karya tulis bapak/ibu guru ke halaman web.</p>
        </div>

        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="judul" class="form-label">Judul Artikel <span>*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul') }}" placeholder="Contoh: Pentingnya Literasi Digital Bagi Siswa Sekolah Dasar" required>
                    @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="select_guru_staff" class="form-label">Pilih Penulis dari Database Guru / Staf</label>
                    <select id="select_guru_staff" class="form-control-admin">
                        <option value="">-- Pilih dari Data Guru / Staf --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" data-nama="{{ $g->nama }}" {{ old('guru_staff_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama }} @if($g->jabatan)({{ $g->jabatan }})@endif
                            </option>
                        @endforeach
                        <option value="custom">-- Tulis Nama Manual / Lainnya --</option>
                    </select>
                    <input type="hidden" name="guru_staff_id" id="guru_staff_id" value="{{ old('guru_staff_id') }}">
                    <div class="form-help">Pilih guru/staf di atas untuk mengisi otomatis nama penulis di bawah.</div>
                </div>

                <div class="form-field">
                    <label for="penulis" class="form-label">Nama Penulis (Guru/Staf) <span>*</span></label>
                    <input type="text" name="penulis" id="penulis" class="form-control-admin @error('penulis') is-invalid @enderror" value="{{ old('penulis') }}" placeholder="Contoh: Dra. Siti Rahmawati, M.Pd" required>
                    @error('penulis')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="kategori" class="form-label">Kategori Artikel <span>*</span></label>
                    <select id="select_kategori" class="form-control-admin mb-2">
                        <option value="">-- Pilih Kategori Utama --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                        <option value="__custom__" {{ old('kategori') && !in_array(old('kategori'), $kategoriList) ? 'selected' : '' }}>+ Tulis Kategori Baru (Manual)</option>
                    </select>
                    <input type="text" name="kategori" id="kategori" class="form-control-admin @error('kategori') is-invalid @enderror" value="{{ old('kategori') }}" placeholder="Atau ketik nama kategori baru di sini..." required>
                    <div class="form-help">Pilih kategori dari daftar atau ketikkan nama kategori kustom Anda.</div>
                    @error('kategori')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="tanggal" class="form-label">Tanggal Publikasi</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control-admin @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}">
                    @error('tanggal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="status" class="form-label">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control-admin @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Terbitkan (Published)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan Draf (Draft)</option>
                    </select>
                    @error('status')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="gambar" class="form-label">Gambar Utama / Header Artikel (Opsional)</label>
                    
                    <!-- Instant Image Preview Box -->
                    <div class="current-image mb-2" id="image-preview-box" style="display: none;">
                        <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            <img src="#" id="image-preview-element" alt="Pratinjau Gambar" style="width: 100%; height: auto; display: block;">
                        </div>
                        <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">Pratinjau gambar baru (belum disimpan).</small>
                    </div>

                    <input type="file" name="gambar" id="gambar" class="form-control-admin form-file @error('gambar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Format: JPG, PNG, WEBP. Maksimal ukuran file: 2MB.</div>
                    @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="isi" class="form-label">Isi Artikel <span>*</span></label>
                    <textarea name="isi" id="isi" class="form-control-admin @error('isi') is-invalid @enderror" rows="12" placeholder="Tuliskan isi artikel karya guru di sini..." required style="line-height: 1.6;">{{ old('isi') }}</textarea>
                    <div class="form-help">Gunakan <code>Enter</code> pada keyboard untuk memisahkan antar paragraf.</div>
                    @error('isi')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-card-footer justify-content-between">
            <a href="{{ route('admin.guru-menulis.index') }}" class="btn-admin secondary">Batal</a>
            <button type="submit" class="btn-admin primary">Simpan Artikel</button>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 280px;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.1/build/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize CKEditor 5
        const isiEl = document.querySelector('#isi');
        if (isiEl && typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(isiEl, {
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
                    editor.model.document.on('change:data', () => {
                        isiEl.value = editor.getData();
                        window.isFormDirty = true;
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Auto Fill Penulis
        const selectGuru = document.getElementById('select_guru_staff');
        const inputPenulis = document.getElementById('penulis');
        const inputGuruId = document.getElementById('guru_staff_id');

        if (selectGuru && inputPenulis) {
            selectGuru.addEventListener('change', function() {
                const selectedOption = selectGuru.options[selectGuru.selectedIndex];
                if (selectGuru.value && selectGuru.value !== 'custom') {
                    const nama = selectedOption.getAttribute('data-nama');
                    if (nama) {
                        inputPenulis.value = nama;
                    }
                    if (inputGuruId) {
                        inputGuruId.value = selectGuru.value;
                    }
                } else if (selectGuru.value === 'custom') {
                    if (inputGuruId) {
                        inputGuruId.value = '';
                    }
                }
            });
        }

        // Kategori Dropdown vs Manual Input
        const selectKategori = document.getElementById('select_kategori');
        const inputKategori = document.getElementById('kategori');

        if (selectKategori && inputKategori) {
            selectKategori.addEventListener('change', function() {
                if (this.value && this.value !== '__custom__') {
                    inputKategori.value = this.value;
                } else if (this.value === '__custom__') {
                    inputKategori.value = '';
                    inputKategori.focus();
                }
            });

            inputKategori.addEventListener('input', function() {
                let matchFound = false;
                for (let i = 0; i < selectKategori.options.length; i++) {
                    if (selectKategori.options[i].value === this.value) {
                        selectKategori.selectedIndex = i;
                        matchFound = true;
                        break;
                    }
                }
                if (!matchFound && this.value.trim() !== '') {
                    selectKategori.value = '__custom__';
                }
            });
        }

        // Instant Image Preview
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
    });
</script>
@endpush
