@extends('adminlte::page')

@section('title', 'Edit Pegawai')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit {{ ucfirst($guru->tipe) }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.guru-staff.index', ['tipe' => $guru->tipe]) }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Dasar {{ ucfirst($guru->tipe) }}</h3>
            </div>
            <form action="{{ route('admin.guru-staff.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $guru->nama) }}" required placeholder="Contoh: Ahmad Dahlan, S.Pd">
                                @error('nama')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="">Pilih jenis kelamin</option>
                                    @foreach($jenisKelamin as $value => $label)
                                        <option value="{{ $value }}" @selected(old('jenis_kelamin', $guru->jenis_kelamin) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kelamin')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $guru->nip) }}" placeholder="Nomor Induk Pegawai (Opsional)">
                                @error('nip')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_kepegawaian">Status Kepegawaian <span class="text-danger">*</span></label>
                                <select name="status_kepegawaian" id="status_kepegawaian" class="form-control @error('status_kepegawaian') is-invalid @enderror" required>
                                    <option value="">Pilih status kepegawaian</option>
                                    @foreach($statusKepegawaian as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status_kepegawaian', $guru->status_kepegawaian) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status_kepegawaian')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <input type="hidden" name="tipe" value="{{ $guru->tipe }}">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jabatan">Jabatan Pokok <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $guru->jabatan) }}" required placeholder="Contoh: Guru Kelas, Wali Kelas, Bendahara">
                                @error('jabatan')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bidang_tugas">Bidang Tugas (Opsional)</label>
                                <input type="text" name="bidang_tugas" id="bidang_tugas" class="form-control @error('bidang_tugas') is-invalid @enderror" value="{{ old('bidang_tugas', $guru->bidang_tugas) }}" placeholder="Contoh: Guru Kelas, Matematika, Tata Usaha">
                                <small class="form-text text-muted">Dapat dikosongkan jika tidak memiliki bidang khusus.</small>
                                @error('bidang_tugas')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pendidikan_terakhir">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control @error('pendidikan_terakhir') is-invalid @enderror" required>
                                    <option value="">Pilih pendidikan terakhir</option>
                                    @foreach($pendidikanTerakhir as $value => $label)
                                        <option value="{{ $value }}" @selected(old('pendidikan_terakhir', $guru->pendidikan_terakhir) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan_terakhir')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agama">Agama <span class="text-danger">*</span></label>
                                <select name="agama" id="agama" class="form-control @error('agama') is-invalid @enderror" required>
                                    <option value="">Pilih agama</option>
                                    @foreach($daftarAgama as $value => $label)
                                        <option value="{{ $value }}" @selected(old('agama', $guru->agama) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('agama')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Saat Ini</label>
                                <div class="mb-2">
                                    @if($guru->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($guru->foto))
                                        <img src="{{ asset('storage/' . $guru->foto) }}" class="img-thumbnail" style="max-height: 100px;" alt="Preview">
                                    @else
                                        <span class="text-muted"><i class="fas fa-image mr-1"></i> Tidak ada foto</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="foto">Ganti Foto Profil</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="foto" accept="image/jpeg,image/png,image/gif">
                                    <label class="custom-file-label" for="foto">Pilih file baru</label>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG. Abaikan jika tidak ingin mengubah foto lama.</small>
                                @error('foto')
                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update {{ ucfirst($guru->tipe) }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $(function () {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endpush
