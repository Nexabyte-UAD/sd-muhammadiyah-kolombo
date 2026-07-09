@php
    $pendidikanRows = old('pendidikan', isset($siswa) ? $siswa->riwayatPendidikan->toArray() : [[]]);
    $pekerjaanRows = old('pekerjaan_alumni', isset($siswa) ? $siswa->riwayatPekerjaan->toArray() : [[]]);
    $pendidikanRows = count($pendidikanRows) ? $pendidikanRows : [[]];
    $pekerjaanRows = count($pekerjaanRows) ? $pekerjaanRows : [[]];
@endphp

<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold mb-0">Riwayat Pendidikan</h6>
        <button type="button" class="btn-admin btn-admin-secondary" id="tambah-pendidikan" style="min-height: 34px; padding: 6px 12px;">
            <i class="fas fa-plus mr-1"></i> Tambah Pendidikan
        </button>
    </div>
    <div id="daftar-pendidikan">
        @foreach($pendidikanRows as $index => $row)
            <div class="card bg-light border riwayat-item mb-2">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-2 mb-2 mb-md-0"><input class="form-control-admin" name="pendidikan[{{ $index }}][jenjang]" value="{{ $row['jenjang'] ?? '' }}" placeholder="Jenjang"></div>
                        <div class="col-md-3 mb-2 mb-md-0"><input class="form-control-admin" name="pendidikan[{{ $index }}][institusi]" value="{{ $row['institusi'] ?? '' }}" placeholder="Sekolah / Universitas"></div>
                        <div class="col-md-3 mb-2 mb-md-0"><input class="form-control-admin" name="pendidikan[{{ $index }}][jurusan]" value="{{ $row['jurusan'] ?? '' }}" placeholder="Jurusan / Program Studi"></div>
                        <div class="col-md-1 mb-2 mb-md-0"><input type="number" class="form-control-admin px-1" name="pendidikan[{{ $index }}][tahun_masuk]" value="{{ $row['tahun_masuk'] ?? '' }}" placeholder="Masuk"></div>
                        <div class="col-md-2 mb-2 mb-md-0"><input type="number" class="form-control-admin" name="pendidikan[{{ $index }}][tahun_selesai]" value="{{ $row['tahun_selesai'] ?? '' }}" placeholder="Tahun selesai"></div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center"><button type="button" class="action-button action-danger hapus-riwayat"><i class="fas fa-times"></i></button></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h6 class="font-weight-bold mb-0">Riwayat Pekerjaan</h6>
        <button type="button" class="btn-admin btn-admin-secondary" id="tambah-pekerjaan" style="min-height: 34px; padding: 6px 12px;">
            <i class="fas fa-plus mr-1"></i> Tambah Pekerjaan
        </button>
    </div>
    <div id="daftar-pekerjaan">
        @foreach($pekerjaanRows as $index => $row)
            <div class="card bg-light border riwayat-item mb-2">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-4 mb-2 mb-md-0"><input class="form-control-admin" name="pekerjaan_alumni[{{ $index }}][pekerjaan]" value="{{ $row['pekerjaan'] ?? '' }}" placeholder="Pekerjaan / Jabatan"></div>
                        <div class="col-md-3 mb-2 mb-md-0"><input class="form-control-admin" name="pekerjaan_alumni[{{ $index }}][perusahaan]" value="{{ $row['perusahaan'] ?? '' }}" placeholder="Perusahaan / Instansi"></div>
                        <div class="col-md-2 mb-2 mb-md-0"><input type="number" class="form-control-admin" name="pekerjaan_alumni[{{ $index }}][tahun_mulai]" value="{{ $row['tahun_mulai'] ?? '' }}" placeholder="Mulai"></div>
                        <div class="col-md-2 mb-2 mb-md-0"><input type="number" class="form-control-admin" name="pekerjaan_alumni[{{ $index }}][tahun_selesai]" value="{{ $row['tahun_selesai'] ?? '' }}" placeholder="Selesai"></div>
                        <div class="col-md-1 d-flex align-items-center justify-content-center"><button type="button" class="action-button action-danger hapus-riwayat"><i class="fas fa-times"></i></button></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    function tambahRiwayat(container, prefix, fields) {
        const index = container.getAttribute('data-next-index') ?? container.querySelectorAll('.riwayat-item').length;
        container.setAttribute('data-next-index', parseInt(index) + 1);
        const columns = fields.map(field =>
            `<div class="${field.col} mb-2 mb-md-0"><input ${field.type ? `type="${field.type}"` : ''} class="form-control-admin" name="${prefix}[${index}][${field.name}]" placeholder="${field.label}"></div>`
        ).join('');
        
        const wrapper = document.createElement('div');
        wrapper.className = 'card bg-light border riwayat-item mb-2';
        wrapper.innerHTML = `<div class="card-body py-3"><div class="row">${columns}<div class="col-md-1 d-flex align-items-center justify-content-center"><button type="button" class="action-button action-danger hapus-riwayat"><i class="fas fa-times"></i></button></div></div></div></div>`;
        container.appendChild(wrapper);
    }

    const tambahPendidikanBtn = document.getElementById('tambah-pendidikan');
    if (tambahPendidikanBtn) {
        tambahPendidikanBtn.addEventListener('click', () => {
            tambahRiwayat(document.getElementById('daftar-pendidikan'), 'pendidikan', [
                {name:'jenjang',label:'Jenjang',col:'col-md-2'}, {name:'institusi',label:'Sekolah / Universitas',col:'col-md-3'},
                {name:'jurusan',label:'Jurusan / Program Studi',col:'col-md-3'}, {name:'tahun_masuk',label:'Masuk',col:'col-md-1',type:'number'},
                {name:'tahun_selesai',label:'Tahun selesai',col:'col-md-2',type:'number'}
            ]);
        });
    }

    const tambahPekerjaanBtn = document.getElementById('tambah-pekerjaan');
    if (tambahPekerjaanBtn) {
        tambahPekerjaanBtn.addEventListener('click', () => {
            tambahRiwayat(document.getElementById('daftar-pekerjaan'), 'pekerjaan_alumni', [
                {name:'pekerjaan',label:'Pekerjaan / Jabatan',col:'col-md-4'}, {name:'perusahaan',label:'Perusahaan / Instansi',col:'col-md-3'},
                {name:'tahun_mulai',label:'Mulai',col:'col-md-2',type:'number'}, {name:'tahun_selesai',label:'Selesai',col:'col-md-2',type:'number'}
            ]);
        });
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('.hapus-riwayat')) {
            event.target.closest('.riwayat-item').remove();
        }
    });
});
</script>
@endpush
@endonce
