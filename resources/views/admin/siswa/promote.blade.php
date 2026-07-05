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
                        @forelse($daftarKelas as $itemKelas)
                        <tr>
                            <td><strong>{{ $itemKelas->tingkat }}</strong></td>
                            <td class="text-right"><span class="badge badge-info px-2 py-1">{{ $rekapSiswa[$itemKelas->tingkat] }} siswa</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Belum ada kelas yang dibuat.</td>
                        </tr>
                        @endforelse
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
                        <li>Kenaikan mengikuti urutan nama kelas pada Data Kelas.</li>
                        <li>Siswa pada kelas terakhir, <strong>{{ $daftarKelas->last()?->tingkat ?? '-' }}</strong>, akan menjadi alumni dengan tahun lulus <strong>{{ date('Y') }}</strong>.</li>
                        <li>Siswa pada setiap kelas lainnya akan dipindahkan ke kelas berikutnya.</li>
                        <li>Siswa baru pada kelas pertama diinput manual setelah proses selesai.</li>
                    </ul>
                </div>

                <div class="callout callout-danger mt-3">
                    <h5><i class="fas fa-skull-crossbones text-danger mr-2"></i> Tindakan Ini Tidak Dapat Dibatalkan!</h5>
                    <p class="mb-0">Pastikan seluruh data siswa sudah benar dan up-to-date sebelum melakukan kenaikan kelas massal. Tindakan ini akan memodifikasi status akademik seluruh siswa aktif sekaligus.</p>
                </div>
            </div>
            <div class="card-footer">
                @if(collect($rekapSiswa)->except('alumni')->sum() > 0)
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
