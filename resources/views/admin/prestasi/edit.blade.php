@extends('layouts.admin')

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
            <x-auto-format-notice />
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
                    <textarea name="deskripsi" id="deskripsi" class="form-control-admin @error('deskripsi') is-invalid @enderror" rows="3" required>{{ old('deskripsi', $prestasi->deskripsi) }}</textarea>
                    @error('deskripsi')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="gambar" class="form-label">Foto / Bukti</label>
                    
                    <div class="current-image" id="image-preview-box" style="display: {{ $prestasi->gambar ? 'flex' : 'none' }}">
                        <img src="{{ $prestasi->gambar ? asset('storage/' . $prestasi->gambar) : '#' }}" id="image-preview-element" alt="Pratinjau Gambar">
                        <div>
                            <strong id="image-preview-title">{{ $prestasi->gambar ? 'Foto saat ini' : 'Pratinjau gambar baru' }}</strong>
                            <small id="image-preview-help">{{ $prestasi->gambar ? 'Pilih file baru jika ingin menggantinya.' : 'Gambar belum disimpan.' }}</small>
                        </div>
                    </div>

                    <input type="file" name="gambar" id="gambar"
                           class="form-control-admin form-file @error('gambar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/gif">
                    <div class="form-help">JPG, PNG, atau GIF. Maksimal 2 MB.</div>
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

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
