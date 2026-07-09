<div class="form-grid">
    <div class="form-field">
        <label for="tingkat" class="form-label">Kelas <span>*</span></label>
        <input type="text" name="tingkat" id="tingkat"
               class="form-control-admin @error('tingkat') is-invalid @enderror"
               value="{{ old('tingkat', $kelas->tingkat ?? '') }}"
               placeholder="Contoh: Kelas 1A" maxlength="100" required>
        @error('tingkat')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="urutan" class="form-label">Urutan Kelas</label>
        <input type="number" name="urutan" id="urutan" min="1" max="999"
               class="form-control-admin @error('urutan') is-invalid @enderror"
               value="{{ old('urutan', $kelas->urutan ?? '') }}"
               placeholder="Contoh: 1">
        @error('urutan')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" id="tahun_ajaran"
               class="form-control-admin @error('tahun_ajaran') is-invalid @enderror"
               value="{{ old('tahun_ajaran', $kelas->tahun_ajaran ?? '') }}"
               placeholder="Contoh: 2026/2027">
        @error('tahun_ajaran')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="kapasitas" class="form-label">Kapasitas Siswa</label>
        <input type="number" name="kapasitas" id="kapasitas" min="1" max="999"
               class="form-control-admin @error('kapasitas') is-invalid @enderror"
               value="{{ old('kapasitas', $kelas->kapasitas ?? '') }}"
               placeholder="Kosongkan jika tidak dibatasi">
        @error('kapasitas')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="jurusan" class="form-label">Jurusan (Opsional)</label>
        <input type="text" name="jurusan" id="jurusan"
               class="form-control-admin @error('jurusan') is-invalid @enderror"
               value="{{ old('jurusan', $kelas->jurusan ?? '') }}"
               placeholder="Isi jika kelas memiliki jurusan" maxlength="100">
        @error('jurusan')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-field">
        <label for="wali_kelas_id" class="form-label">Wali Kelas (Opsional)</label>
        <select name="wali_kelas_id" id="wali_kelas_id"
                class="form-control-admin @error('wali_kelas_id') is-invalid @enderror">
            <option value="">Belum ditentukan</option>
            @foreach($gurus as $guru)
                <option value="{{ $guru->id }}"
                    @selected((string) old('wali_kelas_id', $kelas->wali_kelas_id ?? '') === (string) $guru->id)>
                    {{ $guru->nama }} - {{ $guru->jabatan }}
                </option>
            @endforeach
        </select>
        @error('wali_kelas_id')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>
