@extends('layouts.admin')

@section('title', 'Edit Video Galeri')
@section('page_kicker', 'Galeri Video')
@section('page_title', 'Edit Video Galeri')

@section('content')
    <form action="{{ route('admin.galeri-video.update', $galeriVideo) }}" method="POST" enctype="multipart/form-data" class="admin-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Edit Video Galeri</h2>
            <p>Perbarui informasi atau link video galeri.</p>
        </div>

        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="judul" class="form-label">Judul Video <span>*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul', $galeriVideo->judul) }}" required>
                    @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="kategori" class="form-label">Kategori Video <span>*</span></label>
                    <select name="kategori" id="kategori" class="form-control-admin @error('kategori') is-invalid @enderror" required>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori', $galeriVideo->kategori) === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    @error('kategori')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="tanggal" class="form-label">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control-admin @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', optional($galeriVideo->tanggal)->format('Y-m-d')) }}">
                    @error('tanggal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="youtube_url" class="form-label">URL / Link YouTube Video <span>*</span></label>
                    <input type="url" name="youtube_url" id="youtube_url" class="form-control-admin @error('youtube_url') is-invalid @enderror" value="{{ old('youtube_url', $galeriVideo->youtube_url) }}" required>
                    @error('youtube_url')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label class="form-label">Thumbnail Saat Ini</label>
                    <div class="current-image mb-2" style="max-width: 240px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                        <img src="{{ $galeriVideo->thumbnail_url }}" alt="{{ $galeriVideo->judul }}" style="width: 100%; height: auto;">
                    </div>
                    <label for="thumbnail" class="form-label">Ganti Thumbnail Kustom Baru (Opsional)</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control-admin form-file @error('thumbnail') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Biarkan kosong jika tetap menggunakan thumbnail saat ini / otomatis dari YouTube.</div>
                    @error('thumbnail')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="keterangan" class="form-label">Keterangan / Deskripsi</label>
                    <textarea name="keterangan" id="keterangan" class="form-control-admin @error('keterangan') is-invalid @enderror" rows="4">{{ old('keterangan', $galeriVideo->keterangan) }}</textarea>
                    @error('keterangan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-card-footer justify-content-between">
            <a href="{{ route('admin.galeri-video.index') }}" class="btn-admin secondary">Batal</a>
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
    });
</script>
@endpush
