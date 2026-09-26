@extends('layouts.admin')

@section('title', 'Tambah Foto Galeri')
@section('page_kicker', 'Galeri Foto')
@section('page_title', 'Tambah Foto Baru')

@section('content')
    <form action="{{ route('admin.galeri-foto.store') }}" method="POST" enctype="multipart/form-data" class="admin-card">
        @csrf
        <div class="form-card-header">
            <h2>Tambah Foto Galeri</h2>
            <p>Unggah foto kegiatan sekolah baru ke galeri publik.</p>
        </div>

        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="judul" class="form-label">Judul Foto <span>*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul') }}" placeholder="Contoh: Upacara Bendera HUT RI Ke-81" required>
                    @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="kategori" class="form-label">Kategori Foto <span>*</span></label>
                    <select name="kategori" id="kategori" class="form-control-admin @error('kategori') is-invalid @enderror" required>
                        <option value="">Pilih Kategori...</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    @error('kategori')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control-admin @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}">
                    @error('tanggal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="foto" class="form-label">File Foto <span>*</span></label>
                    
                    <!-- Instant Image Preview Box -->
                    <div class="current-image mb-2" id="image-preview-box" style="display: none;">
                        <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            <img src="#" id="image-preview-element" alt="Pratinjau Gambar" style="width: 100%; height: auto; display: block;">
                        </div>
                        <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">Pratinjau gambar baru (belum disimpan).</small>
                    </div>

                    <input type="file" name="foto" id="foto" class="form-control-admin form-file @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                    <div class="form-help">Format: JPG, PNG, WEBP. Maksimal ukuran file: 2MB.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="keterangan" class="form-label">Keterangan / Deskripsi (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" class="form-control-admin @error('keterangan') is-invalid @enderror" rows="5" placeholder="Tuliskan catatan atau keterangan dokumentasi foto...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-card-footer justify-content-between">
            <a href="{{ route('admin.galeri-foto.index') }}" class="btn-admin secondary">Batal</a>
            <button type="submit" class="btn-admin primary">Simpan Foto</button>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 200px;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.1/build/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize CKEditor 5
        const keteranganEl = document.querySelector('#keterangan');
        if (keteranganEl && typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(keteranganEl, {
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
                        keteranganEl.value = editor.getData();
                        window.isFormDirty = true;
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }

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
