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
        @if(isset($berita) && $berita->gambar)
            <div class="current-image">
                <img src="{{ asset('storage/' . $berita->gambar) }}" alt="Gambar berita saat ini">
                <div>
                    <strong>Gambar saat ini</strong>
                    <small>Pilih file baru hanya jika ingin menggantinya.</small>
                </div>
            </div>
        @endif
        <input type="file" name="gambar" id="gambar"
               class="form-control-admin form-file @error('gambar') is-invalid @enderror"
               accept="image/jpeg,image/png,image/gif">
        <div class="form-help">JPG, PNG, atau GIF. Maksimal 2 MB.</div>
        @error('gambar')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>
