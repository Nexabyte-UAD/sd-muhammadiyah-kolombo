@extends('layouts.admin')

@section('title', 'Status Akhir Tahun')
@section('page_kicker', 'Akademik')
@section('page_title', 'Penetapan Status Akhir Tahun')
@section('page_description', 'Kenaikan kelas, tinggal kelas, kelulusan, dan perpindahan siswa.')

@section('page_actions')
    <a href="{{ route('admin.siswa.index') }}" class="btn-admin btn-admin-secondary">
        Kembali
    </a>
@endsection

@section('content')
    <x-admin-usage-guide
        description="Petunjuk pemrosesan kenaikan kelas, kelulusan, dan mutasi siswa."
        :items="[
            'Pilih Tahun Ajaran aktif dan Kelas Asal siswa yang ingin diproses.',
            'Tentukan keputusan per siswa: Naik Kelas (pilih Kelas Tujuan), Tinggal Kelas, Lulus, atau Pindah/Keluar.',
            'Bagi siswa yang pindah/keluar, tulis nama Sekolah Tujuan dan tanggal efektif mutasi.',
            'Tekan tombol Tinjau dan Proses. Aksi ini akan dicatat permanen dalam riwayat akademik siswa.',
        ]"
    />

    <section class="admin-card mb-4">
        <header class="admin-card-header">
            <div>
                <h2 class="admin-card-title">1. Pilih Periode dan Kelas Asal</h2>
            </div>
        </header>
        <form method="GET" action="{{ route('admin.siswa.promote.page') }}">
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-md-0">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span>*</span></label>
                            <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control-admin"
                                   value="{{ $tahunAjaran }}" pattern="\d{4}/\d{4}" placeholder="2026/2027" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-md-0">
                            <label for="kelas" class="form-label">Kelas Asal <span>*</span></label>
                            <select name="kelas" id="kelas" class="form-control-admin" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                    <option value="{{ $kelas->tingkat }}" @selected($kelasAsal === $kelas->tingkat)>
                                        {{ $kelas->tingkat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-md-0">
                            <label class="form-label" style="visibility: hidden;">&nbsp;</label>
                            <button type="submit" class="btn-admin" style="width: 100%; height: 42px;">
                                Tampilkan Siswa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    @if($kelasAsal)
        <form method="POST" action="{{ route('admin.siswa.promote') }}" id="form-status-akhir">
            @csrf
            <input type="hidden" name="kelas_asal" value="{{ $kelasAsal }}">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

            <section class="admin-card mb-4">
                <header class="admin-card-header">
                    <div>
                        <h2 class="admin-card-title">2. Tentukan Keputusan Siswa — {{ $kelasAsal }}</h2>
                        <div class="admin-card-subtitle">{{ $siswas->count() }} siswa ditemukan</div>
                    </div>
                </header>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px" class="text-center">No</th>
                                <th>Nama Siswa</th>
                                <th style="width: 200px">Keputusan</th>
                                <th style="width: 220px">Kelas Tujuan</th>
                                <th>Catatan / Keterangan Tambahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $siswa)
                                @php($oldStatus = old("keputusan.{$siswa->id}.status", 'naik'))
                                <tr>
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <strong class="text-navy">{{ $siswa->nama }}</strong>
                                        <div class="small text-muted">NIS: {{ $siswa->nis ?: '-' }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <select name="keputusan[{{ $siswa->id }}][status]"
                                                class="form-control-admin keputusan-status"
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
                                                class="form-control-admin kelas-tujuan @error("keputusan.{$siswa->id}.kelas_tujuan") is-invalid @enderror">
                                            <option value="">-- Pilih Tujuan --</option>
                                            @foreach($daftarKelas->where('tingkat', '!=', $kelasAsal) as $kelasTujuan)
                                                <option value="{{ $kelasTujuan->tingkat }}"
                                                    @selected(old("keputusan.{$siswa->id}.kelas_tujuan") === $kelasTujuan->tingkat)>
                                                    {{ $kelasTujuan->tingkat }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("keputusan.{$siswa->id}.kelas_tujuan")
                                            <div class="form-error">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="keputusan[{{ $siswa->id }}][catatan]"
                                               class="form-control-admin" maxlength="500"
                                               value="{{ old("keputusan.{$siswa->id}.catatan") }}"
                                               placeholder="Opsional">
                                        <div class="transfer-fields mt-2" id="transfer-fields-{{ $siswa->id }}" style="display:none">
                                            <input type="text" name="keputusan[{{ $siswa->id }}][sekolah_tujuan]"
                                                   class="form-control-admin mb-2"
                                                   value="{{ old("keputusan.{$siswa->id}.sekolah_tujuan") }}"
                                                   placeholder="Sekolah tujuan">
                                            <input type="date" name="keputusan[{{ $siswa->id }}][tanggal_keluar]"
                                                   class="form-control-admin"
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
                    <footer class="admin-card-footer">
                        <span>Riwayat keputusan, waktu proses, dan pemroses akan dicatat.</span>
                        <button type="submit" class="btn-admin" id="btn-proses">
                            Tinjau dan Proses
                        </button>
                    </footer>
                @endif
            </section>
        </form>
    @endif

    <section class="admin-card">
        <header class="admin-card-header">
            <div>
                <h2 class="admin-card-title">Riwayat Proses Terbaru</h2>
                <div class="admin-card-subtitle">Daftar keputusan status akhir tahun yang telah diproses</div>
            </div>
        </header>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                            <td class="align-middle">{{ $item->tanggal_proses->format('d/m/Y H:i') }}</td>
                            <td class="align-middle">{{ $item->tahun_ajaran }}</td>
                            <td class="align-middle font-weight-bold text-navy">{{ $item->siswa?->nama ?? 'Siswa dihapus' }}</td>
                            <td class="align-middle">{{ $item->kelas_asal ?: '-' }}</td>
                            <td class="align-middle">
                                <span class="badge badge-{{
                                    match($item->keputusan) {
                                        'naik' => 'success',
                                        'tinggal' => 'warning',
                                        'lulus' => 'primary',
                                        default => 'secondary'
                                    }
                                }} px-2 py-1">
                                    {{ match($item->keputusan) {
                                        'naik' => 'Naik Kelas',
                                        'tinggal' => 'Tinggal Kelas',
                                        'lulus' => 'Lulus',
                                        default => 'Pindah / Keluar'
                                    } }}
                                </span>
                            </td>
                            <td class="align-middle">{{ $item->kelas_tujuan ?: '-' }}</td>
                            <td class="align-middle">{{ $item->pemroses?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Belum ada riwayat status akhir tahun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function toggleKelasTujuan(select) {
                const siswaId = select.getAttribute('data-siswa');
                const tujuan = document.getElementById('kelas-tujuan-' + siswaId);
                const perluTujuan = select.value === 'naik';
                const pindah = select.value === 'pindah';
    
                if (tujuan) {
                    tujuan.disabled = !perluTujuan;
                    tujuan.required = perluTujuan;
                    if (!perluTujuan) {
                        tujuan.value = '';
                    }
                }
                
                const transferFields = document.getElementById('transfer-fields-' + siswaId);
                if (transferFields) {
                    transferFields.style.display = pindah ? 'block' : 'none';
                    const sekolahTujuanInput = transferFields.querySelector('input[name*="[sekolah_tujuan]"]');
                    if (sekolahTujuanInput) {
                        sekolahTujuanInput.required = pindah;
                    }
                }
            }
    
            document.querySelectorAll('.keputusan-status').forEach(function (select) {
                toggleKelasTujuan(select);
                select.addEventListener('change', function () {
                    toggleKelasTujuan(select);
                });
            });
    
            const form = document.getElementById('form-status-akhir');
            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!confirm('Proses keputusan akhir tahun untuk seluruh siswa yang ditampilkan? Data akan dicatat dalam riwayat akademik.')) {
                        event.preventDefault();
                        return;
                    }
    
                    const btnProses = document.getElementById('btn-proses');
                    if (btnProses) {
                        btnProses.disabled = true;
                        btnProses.textContent = 'Memproses...';
                    }
                });
            }
        });
    </script>
@endpush
