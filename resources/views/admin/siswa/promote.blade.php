@extends('adminlte::page')

@section('title', 'Kenaikan Kelas Massal')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Kenaikan Kelas & Kelulusan Massal</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Left Column: Summary of Students -->
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Rekapitulasi Jumlah Siswa</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped m-0">
                    <thead>
                        <tr>
                            <th>Kelas / Status</th>
                            <th class="text-right">Jumlah Siswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 6; $i++)
                        <tr>
                            <td><strong>Kelas {{ $i }}</strong></td>
                            <td class="text-right"><span class="badge badge-info px-2 py-1">{{ $rekapSiswa[$i] }} siswa</span></td>
                        </tr>
                        @endfor
                        <tr class="bg-light">
                            <td><strong>Alumni (Telah Lulus)</strong></td>
                            <td class="text-right"><span class="badge badge-success px-2 py-1">{{ $rekapSiswa['alumni'] }} alumni</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Promotion Panel -->
    <div class="col-md-7">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title text-warning"><i class="fas fa-exclamation-triangle mr-2"></i> PERHATIAN & KONFIRMASI</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-info-circle"></i> Cara Kerja Kenaikan Kelas Massal:</h5>
                    <ul class="mb-0 pl-4">
                        <li>Siswa <strong>Kelas 6</strong> akan otomatis dinyatakan <strong>LULUS (Alumni)</strong>, kolom kelas dikosongkan (`null`), dan tahun kelulusan disetel ke tahun ini (<strong>{{ date('Y') }}</strong>).</li>
                        <li>Siswa di <strong>Kelas 5</strong> akan naik ke <strong>Kelas 6</strong>.</li>
                        <li>Siswa di <strong>Kelas 4</strong> akan naik ke <strong>Kelas 5</strong>.</li>
                        <li>Siswa di <strong>Kelas 3</strong> akan naik ke <strong>Kelas 4</strong>.</li>
                        <li>Siswa di <strong>Kelas 2</strong> akan naik ke <strong>Kelas 3</strong>.</li>
                        <li>Siswa di <strong>Kelas 1</strong> akan naik ke <strong>Kelas 2</strong>.</li>
                        <li>Data siswa baru untuk <strong>Kelas 1</strong> yang baru masuk harus diinput manual setelah proses kenaikan kelas ini selesai.</li>
                    </ul>
                </div>

                <div class="callout callout-danger mt-3">
                    <h5><i class="fas fa-skull-crossbones text-danger mr-2"></i> Tindakan Ini Tidak Dapat Dibatalkan!</h5>
                    <p class="mb-0">Pastikan seluruh data siswa sudah benar dan up-to-date sebelum melakukan kenaikan kelas massal. Tindakan ini akan memodifikasi status akademik seluruh siswa aktif sekaligus.</p>
                </div>
            </div>
            <div class="card-footer">
                @if(array_sum(array_slice($rekapSiswa, 0, 6)) > 0)
                    <form action="{{ route('admin.siswa.promote') }}" method="POST" onsubmit="return confirmPromo(event)">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-block btn-lg py-3 font-weight-bold" id="btn-submit-promote">
                            <i class="fas fa-arrow-up mr-2"></i> PROSES KENAIKAN KELAS & KELULUSAN MASSAL
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-block btn-lg py-3 font-weight-bold" disabled>
                        <i class="fas fa-ban mr-2"></i> TIDAK ADA SISWA AKTIF UNTUK DIPROMOSIKAN
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    function confirmPromo(event) {
        event.preventDefault();
        
        let confirm1 = confirm("Apakah Anda benar-benar yakin ingin memproses kenaikan kelas dan kelulusan massal?");
        if (confirm1) {
            let confirm2 = confirm("PERINGATAN KEDUA: Seluruh siswa kelas 6 akan langsung lulus menjadi alumni dan siswa kelas 1-5 akan naik 1 tingkat. Lanjutkan?");
            if (confirm2) {
                // Disable button to prevent double submit
                $('#btn-submit-promote').attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Sedang Memproses Kenaikan Kelas...');
                event.target.submit();
                return true;
            }
        }
        return false;
    }
</script>
@endpush
