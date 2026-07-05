@php
    $pendidikanRows = old('pendidikan', isset($siswa) ? $siswa->riwayatPendidikan->toArray() : [[]]);
    $pekerjaanRows = old('pekerjaan_alumni', isset($siswa) ? $siswa->riwayatPekerjaan->toArray() : [[]]);
    $pendidikanRows = count($pendidikanRows) ? $pendidikanRows : [[]];
    $pekerjaanRows = count($pekerjaanRows) ? $pekerjaanRows : [[]];
@endphp

<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="font-weight-bold mb-0">Riwayat Pendidikan</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="tambah-pendidikan">
            <i class="fas fa-plus mr-1"></i> Tambah Pendidikan
        </button>
    </div>
    <div id="daftar-pendidikan">
        @foreach($pendidikanRows as $index => $row)
            <div class="card bg-light border riwayat-item">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-2"><input class="form-control" name="pendidikan[{{ $index }}][jenjang]" value="{{ $row['jenjang'] ?? '' }}" placeholder="Jenjang"></div>
                        <div class="col-md-3"><input class="form-control" name="pendidikan[{{ $index }}][institusi]" value="{{ $row['institusi'] ?? '' }}" placeholder="Sekolah / Universitas"></div>
                        <div class="col-md-3"><input class="form-control" name="pendidikan[{{ $index }}][jurusan]" value="{{ $row['jurusan'] ?? '' }}" placeholder="Jurusan / Program Studi"></div>
                        <div class="col-md-1"><input type="number" class="form-control px-1" name="pendidikan[{{ $index }}][tahun_masuk]" value="{{ $row['tahun_masuk'] ?? '' }}" placeholder="Masuk"></div>
                        <div class="col-md-2"><input type="number" class="form-control" name="pendidikan[{{ $index }}][tahun_selesai]" value="{{ $row['tahun_selesai'] ?? '' }}" placeholder="Tahun selesai"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-outline-danger hapus-riwayat"><i class="fas fa-times"></i></button></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h6 class="font-weight-bold mb-0">Riwayat Pekerjaan</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="tambah-pekerjaan">
            <i class="fas fa-plus mr-1"></i> Tambah Pekerjaan
        </button>
    </div>
    <div id="daftar-pekerjaan">
        @foreach($pekerjaanRows as $index => $row)
            <div class="card bg-light border riwayat-item">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-4"><input class="form-control" name="pekerjaan_alumni[{{ $index }}][pekerjaan]" value="{{ $row['pekerjaan'] ?? '' }}" placeholder="Pekerjaan / Jabatan"></div>
                        <div class="col-md-3"><input class="form-control" name="pekerjaan_alumni[{{ $index }}][perusahaan]" value="{{ $row['perusahaan'] ?? '' }}" placeholder="Perusahaan / Instansi"></div>
                        <div class="col-md-2"><input type="number" class="form-control" name="pekerjaan_alumni[{{ $index }}][tahun_mulai]" value="{{ $row['tahun_mulai'] ?? '' }}" placeholder="Mulai"></div>
                        <div class="col-md-2"><input type="number" class="form-control" name="pekerjaan_alumni[{{ $index }}][tahun_selesai]" value="{{ $row['tahun_selesai'] ?? '' }}" placeholder="Selesai"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-outline-danger hapus-riwayat"><i class="fas fa-times"></i></button></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@once
@push('js')
<script>
$(function () {
    function tambahRiwayat(container, prefix, fields) {
        const index = container.data('next-index') ?? container.children('.riwayat-item').length;
        container.data('next-index', index + 1);
        const columns = fields.map(field =>
            `<div class="${field.col}"><input ${field.type ? `type="${field.type}"` : ''} class="form-control" name="${prefix}[${index}][${field.name}]" placeholder="${field.label}"></div>`
        ).join('');
        container.append(`<div class="card bg-light border riwayat-item"><div class="card-body py-3"><div class="row">${columns}<div class="col-md-1"><button type="button" class="btn btn-outline-danger hapus-riwayat"><i class="fas fa-times"></i></button></div></div></div></div>`);
    }

    $('#tambah-pendidikan').on('click', () => tambahRiwayat($('#daftar-pendidikan'), 'pendidikan', [
        {name:'jenjang',label:'Jenjang',col:'col-md-2'}, {name:'institusi',label:'Sekolah / Universitas',col:'col-md-3'},
        {name:'jurusan',label:'Jurusan / Program Studi',col:'col-md-3'}, {name:'tahun_masuk',label:'Masuk',col:'col-md-1',type:'number'},
        {name:'tahun_selesai',label:'Tahun selesai',col:'col-md-2',type:'number'}
    ]));
    $('#tambah-pekerjaan').on('click', () => tambahRiwayat($('#daftar-pekerjaan'), 'pekerjaan_alumni', [
        {name:'pekerjaan',label:'Pekerjaan / Jabatan',col:'col-md-4'}, {name:'perusahaan',label:'Perusahaan / Instansi',col:'col-md-3'},
        {name:'tahun_mulai',label:'Mulai',col:'col-md-2',type:'number'}, {name:'tahun_selesai',label:'Selesai',col:'col-md-2',type:'number'}
    ]));
    $(document).on('click', '.hapus-riwayat', function () { $(this).closest('.riwayat-item').remove(); });
});
</script>
@endpush
@endonce
