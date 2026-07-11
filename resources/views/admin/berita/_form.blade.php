{{--
    Formulir Partial Berita (admin/berita/_form.blade.php)
    Menampilkan kolom input berita (judul, tanggal rilis, status publikasi, isi, dan cover berita)
    yang digunakan bersama oleh halaman tambah (create) dan sunting (edit) berita, serta
    mengintegrasikan editor WYSIWYG CKEditor 5 dan skrip pratinjau gambar instant.
--}}
<x-auto-format-notice />

<div class="form-grid">
    <div class="form-field form-field-full">
        <label for="judul" class="form-label">Judul Berita <span>*</span></label>
        <input type="text" name="judul" id="judul"
               class="form-control-admin @error('judul') is-invalid @enderror"
               value="{{ old('judul', optional($berita ?? null)->judul) }}"
               placeholder="Masukkan judul berita" required>
        @error('judul')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="tanggal" class="form-label">Tanggal Rilis <span>*</span></label>
        <input type="date" name="tanggal" id="tanggal"
               class="form-control-admin @error('tanggal') is-invalid @enderror"
               value="{{ old('tanggal', isset($berita) ? \Carbon\Carbon::parse($berita->tanggal)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required>
        @error('tanggal')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="status" class="form-label">Status Publikasi <span>*</span></label>
        <select name="status" id="status" class="form-control-admin @error('status') is-invalid @enderror" required>
            <option value="published" @selected(old('status', optional($berita ?? null)->status ?? 'published') === 'published')>
                Terbit — tampil di website
            </option>
            <option value="draft" @selected(old('status', optional($berita ?? null)->status) === 'draft')>
                Draft — belum ditampilkan
            </option>
        </select>
        @error('status')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field form-field-full">
        <label for="isi" class="form-label">Isi Berita <span>*</span></label>
        <textarea name="isi" id="isi" rows="10"
                  class="form-control-admin @error('isi') is-invalid @enderror"
                  placeholder="Tuliskan isi berita..." required>{{ old('isi', optional($berita ?? null)->isi) }}</textarea>
        @error('isi')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field form-field-full">
        <label for="gambar" class="form-label">{{ isset($berita) ? 'Ganti Gambar' : 'Gambar Berita' }}</label>
        
        <div class="current-image" id="image-preview-box" style="display: {{ (isset($berita) && $berita->gambar) ? 'flex' : 'none' }}">
            <img src="{{ (isset($berita) && $berita->gambar) ? asset('storage/' . $berita->gambar) : '#' }}" id="image-preview-element" alt="Pratinjau Gambar">
            <div>
                <strong id="image-preview-title">{{ isset($berita) ? 'Gambar saat ini' : 'Pratinjau gambar baru' }}</strong>
                <small id="image-preview-help">{{ isset($berita) ? 'Pilih file baru jika ingin menggantinya.' : 'Gambar belum disimpan.' }}</small>
            </div>
        </div>

        <input type="file" name="gambar" id="gambar"
               class="form-control-admin form-file @error('gambar') is-invalid @enderror"
               accept="image/jpeg,image/png,image/gif">
        <div class="form-help">JPG, PNG, atau GIF. Maksimal 2 MB.</div>
        @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>

@push('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 250px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.1/build/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize CKEditor 5
            ClassicEditor
                .create(document.querySelector('#isi'), {
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
                        document.querySelector('#isi').value = editor.getData();
                        window.isFormDirty = true;
                    });
                })
                .catch(error => {
                    console.error(error);
                });

            // Instant Image Preview & Size Validation
            const gambarInput = document.getElementById('gambar');
            if (gambarInput) {
                gambarInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Max size 2MB
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
