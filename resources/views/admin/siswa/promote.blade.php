@extends('layouts.admin')

@section('title', 'Status Akhir Tahun')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Penetapan Status Akhir Tahun</h1>
            <p class="text-muted mb-0">Kenaikan kelas, tinggal kelas, kelulusan, dan perpindahan siswa.</p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
@stop

@section('content')
    <div class="card card-accent">
        <div class="card-header">
            <h3 class="card-title">1. Pilih Periode dan Kelas Asal</h3>
        </div>
        <form method="GET" action="{{ route('admin.siswa.promote.page') }}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-md-0">
                            <label for="tahun_ajaran">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control"
                                   value="{{ $tahunAjaran }}" pattern="\d{4}/\d{4}" placeholder="2026/2027" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-md-0">
                            <label for="kelas">Kelas Asal</label>
                            <select name="kelas" id="kelas" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                    <option value="{{ $kelas->tingkat }}" @selected($kelasAsal === $kelas->tingkat)>
                                        {{ $kelas->tingkat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-users mr-1"></i> Tampilkan Siswa
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if($kelasAsal)
        <form method="POST" action="{{ route('admin.siswa.promote') }}" id="form-status-akhir">
            @csrf
            <input type="hidden" name="kelas_asal" value="{{ $kelasAsal }}">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

            <div class="card card-accent-success">
                <div class="card-header">
                    <h3 class="card-title">2. Tentukan Keputusan Siswa — {{ $kelasAsal }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $siswas->count() }} siswa</span>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 55px">No</th>
                                <th>Nama Siswa</th>
                                <th style="width: 180px">Keputusan</th>
                                <th style="width: 220px">Kelas Tujuan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $siswa)
                                @php($oldStatus = old("keputusan.{$siswa->id}.status", 'naik'))
                                <tr>
                                    <td class="align-middle">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $siswa->nama }}</strong>
                                        <div class="small text-muted">NIS: {{ $siswa->nis ?: '-' }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <select name="keputusan[{{ $siswa->id }}][status]"
                                                class="form-control keputusan-status"
                                                data-siswa="{{ $siswa->id }}" required>
                                            <option value="naik" @selected($oldStatus === 'naik')>Naik Kelas</option>
                                            <option value="tinggal" @selected($oldStatus === 'tinggal')>Tinggal Kelas</option>
                                            <option value="lulus" @selected($oldStatus === 'lulus')>Lulus</option>
                                            <option value="pindah" @selected($oldStatus === 'pindah')>Pindah / Keluar</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <select name="keputusan[{{ $siswa->id }}][kelas_tujuan]"
                                                id="kelas-tujuan-{{ $siswa->id }}"
                                                class="form-control kelas-tujuan @error("keputusan.{$siswa->id}.kelas_tujuan") is-invalid @enderror">
                                            <option value="">-- Pilih Tujuan --</option>
                                            @foreach($daftarKelas->where('tingkat', '!=', $kelasAsal) as $kelasTujuan)
                                                <option value="{{ $kelasTujuan->tingkat }}"
                                                    @selected(old("keputusan.{$siswa->id}.kelas_tujuan") === $kelasTujuan->tingkat)>
                                                    {{ $kelasTujuan->tingkat }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("keputusan.{$siswa->id}.kelas_tujuan")
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="keputusan[{{ $siswa->id }}][catatan]"
                                               class="form-control" maxlength="500"
                                               value="{{ old("keputusan.{$siswa->id}.catatan") }}"
                                               placeholder="Opsional">
                                        <div class="transfer-fields mt-2" id="transfer-fields-{{ $siswa->id }}" style="display:none">
                                            <input type="text" name="keputusan[{{ $siswa->id }}][sekolah_tujuan]"
                                                   class="form-control mb-2"
                                                   value="{{ old("keputusan.{$siswa->id}.sekolah_tujuan") }}"
                                                   placeholder="Sekolah tujuan">
                                            <input type="date" name="keputusan[{{ $siswa->id }}][tanggal_keluar]"
                                                   class="form-control"
                                                   value="{{ old("keputusan.{$siswa->id}.tanggal_keluar", date('Y-m-d')) }}">
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Tidak ada siswa aktif di {{ $kelasAsal }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($siswas->isNotEmpty())
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Riwayat keputusan, waktu proses, dan akun admin akan disimpan.
                        </small>
                        <button type="submit" class="btn btn-success" id="btn-proses">
                            <i class="fas fa-clipboard-check mr-1"></i> Tinjau dan Proses
                        </button>
                    </div>
                @endif
            </div>
        </form>
    @endif

    <div class="card card-accent-muted">
        <div class="card-header">
            <h3 class="card-title">Riwayat Proses Terbaru</h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Tahun Ajaran</th>
                        <th>Siswa</th>
                        <th>Kelas Asal</th>
                        <th>Keputusan</th>
                        <th>Kelas Tujuan</th>
                        <th>Diproses Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        <tr>
                            <td>{{ $item->tanggal_proses->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->tahun_ajaran }}</td>
                            <td>{{ $item->siswa?->nama ?? 'Siswa dihapus' }}</td>
                            <td>{{ $item->kelas_asal ?: '-' }}</td>
                            <td>
                                <span class="badge badge-{{
                                    match($item->keputusan) {
                                        'naik' => 'success',
                                        'tinggal' => 'warning',
                                        'lulus' => 'primary',
                                        default => 'secondary'
                                    }
                                }}">
                                    {{ match($item->keputusan) {
                                        'naik' => 'Naik Kelas',
                                        'tinggal' => 'Tinggal Kelas',
                                        'lulus' => 'Lulus',
                                        default => 'Pindah / Keluar'
                                    } }}
                                </span>
                            </td>
                            <td>{{ $item->kelas_tujuan ?: '-' }}</td>
                            <td>{{ $item->pemroses?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada riwayat status akhir tahun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@push('js')
<script>
    $(function () {
        function toggleKelasTujuan(select) {
            const siswaId = select.data('siswa');
            const tujuan = $('#kelas-tujuan-' + siswaId);
            const perluTujuan = select.val() === 'naik';
            const pindah = select.val() === 'pindah';

            tujuan.prop('disabled', !perluTujuan);
            tujuan.prop('required', perluTujuan);
            if (!perluTujuan) {
                tujuan.val('');
            }
            $('#transfer-fields-' + siswaId).toggle(pindah)
                .find('input[name*="[sekolah_tujuan]"]').prop('required', pindah);
        }

        $('.keputusan-status').each(function () {
            toggleKelasTujuan($(this));
        }).on('change', function () {
            toggleKelasTujuan($(this));
        });

        $('#form-status-akhir').on('submit', function (event) {
            if (!confirm('Proses keputusan akhir tahun untuk seluruh siswa yang ditampilkan? Data akan dicatat dalam riwayat akademik.')) {
                event.preventDefault();
                return;
            }

            $('#btn-proses').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
        });
    });
</script>
@endpush
