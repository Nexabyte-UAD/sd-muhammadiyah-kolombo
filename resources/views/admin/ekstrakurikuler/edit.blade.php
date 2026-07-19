{{--
    Halaman Sunting Program Ekstrakurikuler (admin/ekstrakurikuler/edit.blade.php)
    Menyediakan formulir pembaruan data program ekstrakurikuler terdaftar beserta pratinjau gambar
    yang tersimpan saat ini atau opsi menggantinya dengan foto baru.
--}}
@extends('layouts.admin')

@section('container_class', 'admin-container-narrow')

@section('title', 'Edit Ekstrakurikuler')
@section('page_kicker', 'Konten website · Ekstrakurikuler')
@section('page_title', 'Edit Ekstrakurikuler')
@section('page_description', 'Perbarui informasi kegiatan ekstrakurikuler.')

@section('page_actions')
    <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Kembali</a>
@endsection

@section('content')
    <form action="{{ route('admin.ekstrakurikuler.update', $ekstrakurikuler->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')
        <div class="form-card-header">
            <h2>Informasi Ekstrakurikuler</h2>
            <p>Perubahan akan diterapkan setelah disimpan.</p>
        </div>
        <div class="form-card-body">
            <x-auto-format-notice />
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="nama" class="form-label">Nama Ekstrakurikuler <span>*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control-admin @error('nama') is-invalid @enderror" value="{{ old('nama', $ekstrakurikuler->nama) }}" required>
                    @error('nama')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field">
                    <label for="pembina" class="form-label">Pembina</label>
                    <input type="text" name="pembina" id="pembina" class="form-control-admin @error('pembina') is-invalid @enderror" value="{{ old('pembina', $ekstrakurikuler->pembina) }}">
                    @error('pembina')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="jadwal" class="form-label">Jadwal <span>*</span></label>
                    <input type="text" name="jadwal" id="jadwal" class="form-control-admin @error('jadwal') is-invalid @enderror" value="{{ old('jadwal', $ekstrakurikuler->jadwal) }}" required>
                    @error('jadwal')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="deskripsi" class="form-label">Deskripsi <span>*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control-admin @error('deskripsi') is-invalid @enderror" rows="4" required>{{ old('deskripsi', $ekstrakurikuler->deskripsi) }}</textarea>
                    @error('deskripsi')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="foto" class="form-label">Foto Kegiatan</label>
                    
                    <div class="current-image" id="image-preview-box">
                        <span class="current-image-placeholder"><x-admin-icon name="ekstrakurikuler" size="30"/></span>
                        <img src="{{ $ekstrakurikuler->foto ? asset('storage/' . $ekstrakurikuler->foto) : '#' }}" id="image-preview-element" alt="Pratinjau Gambar">
                        <div>
                            <strong id="image-preview-title">{{ $ekstrakurikuler->foto ? 'Foto saat ini' : 'Pratinjau gambar baru' }}</strong>
                            <small id="image-preview-help">{{ $ekstrakurikuler->foto ? 'Pilih file baru jika ingin menggantinya.' : 'Gambar belum disimpan.' }}</small>
                        </div>
                    </div>

                    <input type="file" name="foto" id="foto"
                           class="form-control-admin form-file @error('foto') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/gif">
                    <div class="form-help">JPG, PNG, atau GIF. Maksimal 2 MB.</div>
                    @error('foto')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-card-footer">
            <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn-admin btn-admin-secondary btn-cancel">Batal</a>
            <button type="submit" class="btn-admin">Simpan Perubahan</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
