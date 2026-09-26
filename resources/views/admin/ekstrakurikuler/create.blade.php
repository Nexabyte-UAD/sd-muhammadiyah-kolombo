{{--
    Halaman Tambah Program Ekstrakurikuler Baru (admin/ekstrakurikuler/create.blade.php)
    Menyediakan form pendaftaran program ekstrakurikuler sekolah baru, lengkap dengan input nama kegiatan,
    nama pembina, jadwal rutin, deskripsi lengkap, serta upload foto kegiatan berpratinjau instan.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Tambah Ekstrakurikuler')
@section('page_kicker', 'Konten website · Ekstrakurikuler')
@section('page_title', 'Tambah Ekstrakurikuler')
@section('page_description', 'Tambahkan program ekstrakurikuler baru.')

@section('page_actions')
    <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        <div class="form-card-header">
            <h2>Informasi Ekstrakurikuler</h2>
            <p>Kolom bertanda bintang wajib diisi.</p>
        </div>
        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="nama" class="form-label">Nama Ekstrakurikuler <span>*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control-admin @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama kegiatan">
                    @error('nama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="pembina" class="form-label">Pembina</label>
                    <input type="text" name="pembina" id="pembina" class="form-control-admin @error('pembina') is-invalid @enderror" value="{{ old('pembina') }}" placeholder="Masukkan nama pembina">
                    @error('pembina')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="jadwal" class="form-label">Jadwal <span>*</span></label>
                    <input type="text" name="jadwal" id="jadwal" class="form-control-admin @error('jadwal') is-invalid @enderror" value="{{ old('jadwal') }}" required placeholder="Contoh: Setiap Sabtu, 15:00">
                    @error('jadwal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="deskripsi" class="form-label">Deskripsi <span>*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control-admin @error('deskripsi') is-invalid @enderror" rows="6" placeholder="Tuliskan deskripsi lengkap kegiatan ekstrakurikuler..." required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="foto" class="form-label">Foto Kegiatan (Opsional)</label>
                    
                    <!-- Instant Image Preview Box -->
                    <div class="current-image mb-2" id="image-preview-box" style="display: none;">
                        <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            <img src="#" id="image-preview-element" alt="Pratinjau Gambar" style="width: 100%; height: auto; display: block;">
                        </div>
                        <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">Pratinjau gambar baru (belum disimpan).</small>
                    </div>

                    <input type="file" name="foto" id="foto"
                           class="form-control-admin form-file @error('foto') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Format: JPG, PNG, WEBP. Maksimal ukuran file: 2MB.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Ekstra</button>
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
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize CKEditor 5
            const deskripsiEl = document.querySelector('#deskripsi');
            if (deskripsiEl && typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(deskripsiEl, {
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
                            deskripsiEl.value = editor.getData();
                            window.isFormDirty = true;
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
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
