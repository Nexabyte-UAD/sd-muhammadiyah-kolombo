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
