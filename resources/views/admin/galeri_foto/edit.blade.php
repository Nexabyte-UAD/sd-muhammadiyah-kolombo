@extends('layouts.admin')

@section('title', 'Edit Foto Galeri')
@section('page_kicker', 'Galeri Foto')
@section('page_title', 'Edit Foto Galeri')

@section('content')
    <form action="{{ route('admin.galeri-foto.update', $galeriFoto) }}" method="POST" enctype="multipart/form-data" class="admin-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Edit Foto Galeri</h2>
            <p>Perbarui informasi atau ganti file foto galeri.</p>
        </div>

        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="judul" class="form-label">Judul Foto <span>*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul', $galeriFoto->judul) }}" required>
                    @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="kategori" class="form-label">Kategori Foto <span>*</span></label>
                    <select name="kategori" id="kategori" class="form-control-admin @error('kategori') is-invalid @enderror" required>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori', $galeriFoto->kategori) === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    @error('kategori')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control-admin @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', optional($galeriFoto->tanggal)->format('Y-m-d')) }}">
                    @error('tanggal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label class="form-label">Foto Saat Ini / Pratinjau Baru</label>
                    <div class="current-image mb-2" id="image-preview-box">
                        <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            @if($galeriFoto->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($galeriFoto->foto))
                                <img src="{{ asset('storage/' . $galeriFoto->foto) }}" id="image-preview-element" alt="{{ $galeriFoto->judul }}" style="width: 100%; height: auto; display: block;">
                            @else
                                <img src="{{ asset('assets/images/no-image-available.jpg') }}" id="image-preview-element" alt="Default" style="width: 100%; height: auto; display: block;">
                            @endif
                        </div>
                        <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">
                            {{ $galeriFoto->foto ? 'Gambar tersimpan saat ini.' : 'Belum ada gambar.' }}
                        </small>
                    </div>
                    <label for="foto" class="form-label">Ganti Foto Baru (Opsional)</label>
                    <input type="file" name="foto" id="foto" class="form-control-admin form-file @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Biarkan kosong jika tidak ingin mengubah file foto. Maksimal 2MB.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="keterangan" class="form-label">Keterangan / Deskripsi (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" class="form-control-admin @error('keterangan') is-invalid @enderror" rows="5" placeholder="Tuliskan catatan atau keterangan dokumentasi foto...">{{ old('keterangan', $galeriFoto->keterangan) }}</textarea>
                    @error('keterangan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-card-footer justify-content-between">
            <a href="{{ route('admin.galeri-foto.index') }}" class="btn-admin secondary">Batal</a>
            <button type="submit" class="btn-admin primary">Simpan Perubahan</button>
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
