<div class="col-md-12" id="div-data-keluar" style="display:none;">
    <hr class="my-4">
    <h5>Data Pindah / Keluar</h5>
    <div class="row">
        <div class="col-md-4 form-group">
            <label>Tanggal Keluar <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_keluar" class="form-control @error('tanggal_keluar') is-invalid @enderror"
                   value="{{ old('tanggal_keluar', isset($siswa) ? $siswa->tanggal_keluar?->format('Y-m-d') : date('Y-m-d')) }}">
            @error('tanggal_keluar')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="col-md-4 form-group">
            <label>Sekolah Tujuan <span class="text-danger">*</span></label>
            <input type="text" name="sekolah_tujuan" class="form-control @error('sekolah_tujuan') is-invalid @enderror"
                   value="{{ old('sekolah_tujuan', $siswa->sekolah_tujuan ?? '') }}">
            @error('sekolah_tujuan')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="col-md-4 form-group">
            <label>Alasan Keluar</label>
            <input type="text" name="alasan_keluar" class="form-control @error('alasan_keluar') is-invalid @enderror"
                   value="{{ old('alasan_keluar', $siswa->alasan_keluar ?? '') }}">
            @error('alasan_keluar')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
