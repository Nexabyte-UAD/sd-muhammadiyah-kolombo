<div class="col-md-12" id="div-data-keluar" style="display:none;">
    <hr class="my-4">
    <h5>Data Keluar</h5>
    <div class="row">
        <div class="col-md-4 form-group">
            <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_keluar" class="form-control-admin @error('tanggal_keluar') is-invalid @enderror"
                   value="{{ old('tanggal_keluar', isset($siswa) ? $siswa->tanggal_keluar?->format('Y-m-d') : date('Y-m-d')) }}">
            @error('tanggal_keluar')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 form-group">
            <label class="form-label">Sekolah Tujuan <span class="text-danger">*</span></label>
            <input type="text" name="sekolah_tujuan" class="form-control-admin @error('sekolah_tujuan') is-invalid @enderror"
                   value="{{ old('sekolah_tujuan', $siswa->sekolah_tujuan ?? '') }}">
            @error('sekolah_tujuan')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 form-group">
            <label class="form-label">Alasan Keluar</label>
            <input type="text" name="alasan_keluar" class="form-control-admin @error('alasan_keluar') is-invalid @enderror"
                   value="{{ old('alasan_keluar', $siswa->alasan_keluar ?? '') }}">
            @error('alasan_keluar')<div class="form-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
