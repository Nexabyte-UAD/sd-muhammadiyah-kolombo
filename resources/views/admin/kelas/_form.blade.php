<div class="form-group">
    <label for="tingkat">Kelas <span class="text-danger">*</span></label>
    <input type="text" name="tingkat" id="tingkat"
           class="form-control @error('tingkat') is-invalid @enderror"
           value="{{ old('tingkat', $kelas->tingkat ?? '') }}"
           placeholder="Contoh: Kelas 1A" maxlength="100" required>
    @error('tingkat')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="urutan">Urutan Kelas</label>
    <input type="number" name="urutan" id="urutan" min="1" max="999"
           class="form-control @error('urutan') is-invalid @enderror"
           value="{{ old('urutan', $kelas->urutan ?? '') }}"
           placeholder="Contoh: 1">
    @error('urutan')<span class="invalid-feedback">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label for="tahun_ajaran">Tahun Ajaran</label>
    <input type="text" name="tahun_ajaran" id="tahun_ajaran"
           class="form-control @error('tahun_ajaran') is-invalid @enderror"
           value="{{ old('tahun_ajaran', $kelas->tahun_ajaran ?? '') }}"
           placeholder="Contoh: 2026/2027">
    @error('tahun_ajaran')<span class="invalid-feedback">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label for="kapasitas">Kapasitas Siswa</label>
    <input type="number" name="kapasitas" id="kapasitas" min="1" max="999"
           class="form-control @error('kapasitas') is-invalid @enderror"
           value="{{ old('kapasitas', $kelas->kapasitas ?? '') }}"
           placeholder="Kosongkan jika tidak dibatasi">
    @error('kapasitas')<span class="invalid-feedback">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label for="jurusan">Jurusan (Opsional)</label>
    <input type="text" name="jurusan" id="jurusan"
           class="form-control @error('jurusan') is-invalid @enderror"
           value="{{ old('jurusan', $kelas->jurusan ?? '') }}"
           placeholder="Isi jika kelas memiliki jurusan" maxlength="100">
    @error('jurusan')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>

<div class="form-group">
    <label for="wali_kelas_id">Wali Kelas (Opsional)</label>
    <select name="wali_kelas_id" id="wali_kelas_id"
            class="form-control @error('wali_kelas_id') is-invalid @enderror">
        <option value="">Belum ditentukan</option>
        @foreach($gurus as $guru)
            <option value="{{ $guru->id }}"
                @selected((string) old('wali_kelas_id', $kelas->wali_kelas_id ?? '') === (string) $guru->id)>
                {{ $guru->nama }} - {{ $guru->jabatan }}
            </option>
        @endforeach
    </select>
    @error('wali_kelas_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>
