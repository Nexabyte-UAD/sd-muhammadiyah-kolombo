@extends('layouts.admin')

@section('title', 'Tambah Video Galeri')
@section('page_kicker', 'Galeri Video')
@section('page_title', 'Tambah Video Baru')

@section('content')
    <form action="{{ route('admin.galeri-video.store') }}" method="POST" enctype="multipart/form-data" class="admin-card">
        @csrf
        <div class="form-card-header">
            <h2>Tambah Video Galeri</h2>
            <p>Masukkan link YouTube video kegiatan sekolah baru ke galeri publik.</p>
        </div>

        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="judul" class="form-label">Judul Video <span>*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul') }}" placeholder="Contoh: Dokudrama Pentas Seni Budaya SD Muhammadiyah Kolombo" required>
                    @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="kategori" class="form-label">Kategori Video <span>*</span></label>
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
                    <label for="youtube_url" class="form-label">URL / Link YouTube Video <span>*</span></label>
                    <input type="url" name="youtube_url" id="youtube_url" class="form-control-admin @error('youtube_url') is-invalid @enderror" value="{{ old('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/..." required>
                    <div class="form-help">Copy dan paste link video YouTube dari browser (thumbnail akan otomatis terdeteksi dari YouTube).</div>
                    @error('youtube_url')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="thumbnail" class="form-label">Gambar Thumbnail Kustom (Opsional)</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control-admin form-file @error('thumbnail') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Biarkan kosong untuk menggunakan thumbnail bawaan YouTube otomatis.</div>
                    @error('thumbnail')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="keterangan" class="form-label">Keterangan / Deskripsi (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" class="form-control-admin @error('keterangan') is-invalid @enderror" rows="4" placeholder="Tuliskan catatan singkat mengenai video ini...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-card-footer justify-content-between">
            <a href="{{ route('admin.galeri-video.index') }}" class="btn-admin secondary">Batal</a>
            <button type="submit" class="btn-admin primary">Simpan Video</button>
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
