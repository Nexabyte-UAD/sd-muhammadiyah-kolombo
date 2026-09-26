{{--
    Halaman Sunting Data Prestasi (admin/prestasi/edit.blade.php)
    Menyediakan formulir pembaruan data prestasi siswa terdaftar, lengkap dengan pratinjau bukti foto
    yang saat ini tersimpan di database serta opsi penggantian dengan berkas foto baru.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Edit Prestasi')
@section('page_kicker', 'Konten website · Prestasi')
@section('page_title', 'Edit Prestasi')
@section('page_description', 'Perbarui catatan prestasi siswa.')

@section('page_actions')
    <a href="{{ route('admin.prestasi.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.prestasi.update', $prestasi->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Informasi Prestasi</h2>
            <p>Perubahan akan diterapkan setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="judul" class="form-label">Nama Lomba <span>*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control-admin @error('judul') is-invalid @enderror" value="{{ old('judul', $prestasi->judul) }}" required>
                    @error('judul')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="kategori" class="form-label">Kategori <span>*</span></label>
                    <select name="kategori" id="kategori" class="form-control-admin @error('kategori') is-invalid @enderror" required>
                        @foreach($kategoriPrestasi as $value => $label)
                            <option value="{{ $value }}" @selected(old('kategori', $prestasi->kategori) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="siswa_id" class="form-label">Nama Siswa <span>*</span></label>
                    <select name="siswa_id" id="siswa_id" class="form-control-admin @error('siswa_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}" @selected((string) old('siswa_id', $prestasi->siswa_id) === (string) $siswa->id)>
                                {{ $siswa->nama }}{{ $siswa->kelas ? ' — '.$siswa->kelas : ' — Alumni' }}
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="prestasi_medali" class="form-label">Prestasi / Medali <span>*</span></label>
                    <input type="text" name="prestasi_medali" id="prestasi_medali" class="form-control-admin @error('prestasi_medali') is-invalid @enderror" value="{{ old('prestasi_medali', $prestasi->prestasi_medali) }}" required>
                    @error('prestasi_medali')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="penyelenggara" class="form-label">Penyelenggara <span>*</span></label>
                    <input type="text" name="penyelenggara" id="penyelenggara" class="form-control-admin @error('penyelenggara') is-invalid @enderror" value="{{ old('penyelenggara', $prestasi->penyelenggara) }}" required>
                    @error('penyelenggara')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="tanggal" class="form-label">Tanggal Pelaksanaan <span>*</span></label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control-admin @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $prestasi->tanggal ? \Carbon\Carbon::parse($prestasi->tanggal)->format('Y-m-d') : '') }}" required>
                    @error('tanggal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="deskripsi" class="form-label">Keterangan / Tingkat <span>*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control-admin @error('deskripsi') is-invalid @enderror" rows="5" placeholder="Tuliskan keterangan / tingkat prestasi..." required>{{ old('deskripsi', $prestasi->deskripsi) }}</textarea>
                    @error('deskripsi')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label class="form-label">Gambar Saat Ini / Pratinjau Baru</label>
                    
                    <!-- Instant Image Preview Box -->
                    <div class="current-image mb-2" id="image-preview-box">
                        <div style="max-width: 280px; border-radius: 8px; overflow: hidden; border: 1px solid var(--admin-border);">
                            @if($prestasi->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($prestasi->gambar))
                                <img src="{{ asset('storage/' . $prestasi->gambar) }}" id="image-preview-element" alt="{{ $prestasi->judul }}" style="width: 100%; height: auto; display: block;">
                            @else
                                <img src="{{ asset('assets/images/no-image-available.jpg') }}" id="image-preview-element" alt="Default" style="width: 100%; height: auto; display: block;">
                            @endif
                        </div>
                        <small id="image-preview-help" class="form-help text-primary mt-1" style="display: block; font-weight: 500;">
                            {{ $prestasi->gambar ? 'Gambar tersimpan saat ini.' : 'Belum ada gambar.' }}
                        </small>
                    </div>

                    <label for="gambar" class="form-label">Ganti Foto / Bukti Baru (Opsional)</label>
                    <input type="file" name="gambar" id="gambar"
                           class="form-control-admin form-file @error('gambar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-help">Biarkan kosong jika tidak ingin mengubah foto. Maksimal 2MB.</div>
                    @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.prestasi.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
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
