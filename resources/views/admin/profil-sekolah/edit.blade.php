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
            <x-auto-format-notice />
            <div class="form-grid">
                @if($type === 'akreditasi')
                    <!-- Khusus Akreditasi: Hanya Upload Gambar Sertifikat -->
                    <input type="hidden" name="judul" value="{{ old('judul', $profil->judul ?: 'Sertifikat Akreditasi') }}">
                    <input type="hidden" name="konten" value="{{ old('konten', $profil->konten ?: '-') }}">

                    <div class="form-field form-field-full">
                        <label class="form-label">Gambar Sertifikat Saat Ini</label>
                        <div class="current-image" id="image-preview-box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; padding: 20px; border: 1px solid var(--admin-border); border-radius: 8px;">
                            @if($profil->gambar)
                                <img src="{{ asset('storage/' . $profil->gambar) }}" id="image-preview-element" alt="Sertifikat Akreditasi" style="max-height: 450px; width: auto; object-fit: contain; border-radius: 8px;">
                            @else
                                <div id="image-placeholder" style="text-align: center; padding: 40px 0; color: #94a3b8;">
                                    <x-admin-icon name="image" size="64" style="display: block; margin: 0 auto 12px;"/>
                                    <p class="mb-0">Belum ada file gambar sertifikat yang diunggah.</p>
                                </div>
                                <img src="#" id="image-preview-element" alt="Pratinjau Gambar" style="display: none; max-height: 450px; width: auto; object-fit: contain; border-radius: 8px;">
                            @endif
                        </div>
                    </div>

                    <div class="form-field form-field-full">
                        <label for="gambar" class="form-label">Unggah Gambar Sertifikat Baru <span>*</span></label>
                        <input type="file" name="gambar" id="gambar" class="form-control-admin form-file @error('gambar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg" required>
                        <div class="form-help">Format: JPG, JPEG, PNG. Maksimal ukuran file: 2MB. Mengunggah gambar baru akan menimpa sertifikat lama.</div>
                        @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                @else
                    <!-- Tipe Umum (Visi Misi, Sejarah, dsb.) -->
                    <div class="form-field form-field-full">
                        <label for="judul" class="form-label">Judul Halaman <span>*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul', $profil->judul) }}" required>
                        @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

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
                    @else
                        <div class="form-field form-field-full">
                            <label for="konten" class="form-label">Isi Konten / Penjelasan <span>*</span></label>
                            <textarea name="konten" id="konten" class="form-control-admin @error('konten') is-invalid @enderror" rows="12" placeholder="Ketik isi dari halaman ini..." required style="line-height: 1.6;">{{ old('konten', $profil->konten) }}</textarea>
                            <div class="form-help">Gunakan tombol <code>Enter</code> pada keyboard untuk memisahkan paragraf satu dengan yang lainnya.</div>
                            @error('konten')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    @if($type !== 'visi_misi')
                        <div class="form-field form-field-full">
                            <label class="form-label">Gambar Saat Ini</label>
                            
                            <div class="current-image" id="image-preview-box" style="display: {{ $profil->gambar ? 'flex' : 'none' }}">
                                <img src="{{ $profil->gambar ? asset('storage/' . $profil->gambar) : '#' }}" id="image-preview-element" alt="Pratinjau Gambar">
                                <div>
                                    <strong id="image-preview-title">{{ $profil->gambar ? 'Gambar saat ini' : 'Pratinjau gambar baru' }}</strong>
                                    <small id="image-preview-help">{{ $profil->gambar ? 'Pilih file baru jika ingin menggantinya.' : 'Gambar belum disimpan.' }}</small>
                                </div>
                            </div>
                            
                            @if(!$profil->gambar)
                                <div id="image-placeholder" style="padding: 20px; background: #f8fafc; border: 1px solid var(--admin-border); border-radius: 8px; text-align: center; color: #94a3b8; margin-bottom: 12px;">
                                    <x-admin-icon name="camera" size="48" style="display: block; margin: 0 auto 8px;"/>
                                    <p class="mb-0">Belum ada gambar yang diunggah.</p>
                                </div>
                            @endif
                        </div>

                        <div class="form-field form-field-full">
                            <label for="gambar" class="form-label">Unggah Gambar Baru</label>
                            <input type="file" name="gambar" id="gambar" class="form-control-admin form-file @error('gambar') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                            <div class="form-help">Format: JPG, PNG. Maksimal ukuran file: 2MB. Mengunggah gambar baru akan otomatis menimpa gambar lama.</div>
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
    @if(!in_array($type, ['akreditasi', 'visi_misi'], true))
        <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.1/build/ckeditor.js"></script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(!in_array($type, ['akreditasi', 'visi_misi'], true))
                // Initialize CKEditor 5
                ClassicEditor
                    .create(document.querySelector('#konten'), {
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
                            document.querySelector('#konten').value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
            @endif

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
                            const previewTitle = document.getElementById('image-preview-title');
                            const previewHelp = document.getElementById('image-preview-help');
                            const placeholder = document.getElementById('image-placeholder');

                            if (previewEl) {
                                previewEl.src = e.target.result;
                                previewEl.style.display = 'block';
                            }
                            if (previewBox) {
                                previewBox.style.display = 'flex';
                            }
                            if (placeholder) {
                                placeholder.style.display = 'none';
                            }
                            if (previewTitle) previewTitle.textContent = 'Pratinjau gambar baru';
                            if (previewHelp) previewHelp.textContent = 'Gambar terpilih (belum disimpan).';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
