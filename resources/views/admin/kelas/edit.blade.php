@extends('adminlte::page')

@section('title', 'Pengaturan Kelas')

@section('content_header')
    <h1 class="m-0 text-dark">Data Kelas & Wali Kelas</h1>
@stop

@section('content')
<div class="card card-primary card-outline">
    <form action="{{ route('admin.kelas.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <p class="text-muted">
                Pilih wali kelas dari Data Guru yang sudah tersimpan. Perubahan nama guru akan otomatis ikut diperbarui.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Kelas</th>
                            <th>Wali Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(range(1, 6) as $tingkat)
                            <tr>
                                <td class="align-middle font-weight-bold">Kelas {{ $tingkat }}</td>
                                <td>
                                    <select name="wali_kelas[{{ $tingkat }}]"
                                            class="form-control @error("wali_kelas.{$tingkat}") is-invalid @enderror">
                                        <option value="">Belum ditentukan</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}"
                                                @selected((string) old("wali_kelas.{$tingkat}", optional($kelas->get((string) $tingkat))->wali_kelas_id) === (string) $guru->id)>
                                                {{ $guru->nama }} — {{ $guru->jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("wali_kelas.{$tingkat}")
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan Wali Kelas
            </button>
        </div>
    </form>
</div>
@stop
