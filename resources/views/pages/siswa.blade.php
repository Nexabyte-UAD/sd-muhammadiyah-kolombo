@extends('layouts.public')

@section('content')
<x-breadcrumb>Kesiswaan: Data Siswa</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <h2 class="fw-bold text-dark mb-0">
                    Daftar Siswa @if($kelas) {{ $kelas }} @endif
                </h2>
                @if($kelas)
                    <p class="text-secondary small mt-1">
                        Menampilkan data siswa aktif untuk {{ $kelas }}. <a href="{{ route('siswa') }}" class="text-primary text-decoration-none fw-semibold">Lihat semua kelas</a>.
                    </p>
                @endif
            </div>
        </div>

        <!-- Table Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Controls Row -->
                <div class="row align-items-center mb-3">
                    <!-- Show Entries -->
                    <div class="col-6 col-sm-6">
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="text-secondary small">Show</span>
                            <select id="show-entries" class="form-select form-select-sm border-secondary-subtle" style="width: 75px;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-secondary small">entries</span>
                        </div>
                    </div>
                    <!-- Search -->
                    <div class="col-6 col-sm-6 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2 w-100">
                            <span class="text-secondary small">Search:</span>
                            <input type="text" id="search-input" class="form-control form-control-sm border-secondary-subtle" style="width: 180px;" value="{{ $search }}">
                        </div>
                    </div>
                </div>

                <!-- Simple Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center" id="table-siswa" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5" style="width: 80px;">No</th>
                                <th class="py-2.5">Nama</th>
                                <th class="py-2.5" style="width: 150px;">Kelas</th>
                                <th class="py-2.5" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $index => $item)
                            <tr>
                                <td class="py-2.5 td-no">{{ $index + 1 }}</td>
                                <td class="py-2.5 text-start fw-semibold text-dark ps-4">{{ $item->nama }}</td>
                                <td class="py-2.5 text-secondary">{{ $item->kelas }}</td>
                                <td class="py-2.5">
                                    <button class="btn btn-sm btn-outline-primary rounded-3 px-3 py-1 btn-detail" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailSiswaModal" 
                                            data-nama="{{ $item->nama }}"
                                            data-nis="{{ $item->nis ?? '-' }}"
                                            data-nisn="{{ $item->nisn ?? '-' }}"
                                            data-kelas="{{ $item->kelas }}"
                                            data-jk="{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}"
                                            data-ttl="{{ $item->tempat_lahir ?? '-' }}, {{ $item->tanggal_lahir ? $item->tanggal_lahir->translatedFormat('d F Y') : '-' }}"
                                            data-alamat="{{ $item->alamat ?? '-' }}"
                                            data-foto="{{ $item->foto ? asset('storage/' . $item->foto) : '' }}"
                                            data-huruf="{{ substr($item->nama, 0, 1) }}">
                                        <i class="bi bi-eye-fill me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr class="no-data-row">
                                <td colspan="4" class="py-4 text-center text-muted">No data available in table</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detail Siswa -->
<div class="modal fade" id="detailSiswaModal" tabindex="-1" aria-labelledby="detailSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 bg-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="detailSiswaModalLabel">Detail Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <!-- Foto Siswa / Avatar -->
                <div class="mb-4 d-flex justify-content-center">
                    <div id="modal-foto-container" class="rounded-circle overflow-hidden shadow-sm border" style="width: 130px; height: 130px; display: none;">
                        <img id="modal-foto" src="" alt="Foto Siswa" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div id="modal-avatar" class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 130px; height: 130px; font-size: 3.5rem; font-weight: bold;">
                        A
                    </div>
                </div>

                <h4 id="modal-nama" class="fw-bold text-dark mb-1">Nama Siswa</h4>
                <p id="modal-kelas-badge" class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold mb-4"></p>

                <div class="text-start border-top pt-3">
                    <div class="row g-2 small">
                        <div class="col-4 text-secondary">NIS</div>
                        <div class="col-8 text-dark fw-medium" id="modal-nis">-</div>
                        
                        <div class="col-4 text-secondary">NISN</div>
                        <div class="col-8 text-dark fw-medium" id="modal-nisn">-</div>

                        <div class="col-4 text-secondary">Jenis Kelamin</div>
                        <div class="col-8 text-dark fw-medium" id="modal-jk">-</div>

                        <div class="col-4 text-secondary">Tempat, Tgl Lahir</div>
                        <div class="col-8 text-dark fw-medium" id="modal-ttl">-</div>

                        <div class="col-4 text-secondary">Alamat</div>
                        <div class="col-8 text-dark fw-medium" id="modal-alamat">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4 justify-content-center">
                <button type="button" class="btn btn-secondary rounded-3 px-4 py-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search-input");
        const showEntries = document.getElementById("show-entries");
        const tableBody = document.querySelector("#table-siswa tbody");
        
        // Find existing rows
        const originalRows = Array.from(tableBody.querySelectorAll("tr:not(.no-data-row)"));
        const noDataRow = tableBody.querySelector(".no-data-row");

        function filterTable() {
            const query = searchInput.value.toLowerCase().trim();
            const limit = parseInt(showEntries.value);
            
            let visibleCount = 0;
            let matchCount = 0;
            
            originalRows.forEach(row => {
                const cells = Array.from(row.querySelectorAll("td"));
                const nama = cells[1] ? cells[1].textContent.toLowerCase() : "";
                const kelas = cells[2] ? cells[2].textContent.toLowerCase() : "";
                
                const matchesSearch = nama.includes(query) || kelas.includes(query);
                
                if (matchesSearch) {
                    matchCount++;
                    if (visibleCount < limit) {
                        row.style.display = "";
                        visibleCount++;
                        // Update dynamic row number
                        const noCell = row.querySelector(".td-no");
                        if (noCell) {
                            noCell.textContent = visibleCount;
                        }
                    } else {
                        row.style.display = "none";
                    }
                } else {
                    row.style.display = "none";
                }
            });

            // Handle Empty State
            if (matchCount === 0) {
                if (!noDataRow) {
                    const newNoDataRow = document.createElement("tr");
                    newNoDataRow.className = "no-data-row";
                    newNoDataRow.innerHTML = `<td colspan="4" class="py-4 text-center text-muted">No data available in table</td>`;
                    tableBody.appendChild(newNoDataRow);
                } else {
                    noDataRow.style.display = "";
                }
            } else {
                if (noDataRow) {
                    noDataRow.style.display = "none";
                }
            }
        }

        if (searchInput) {
            searchInput.addEventListener("input", filterTable);
        }
        if (showEntries) {
            showEntries.addEventListener("change", filterTable);
        }
        
        // Run initially
        filterTable();

        // Modal Detail Binding
        const detailButtons = document.querySelectorAll(".btn-detail");
        const modalFotoContainer = document.getElementById("modal-foto-container");
        const modalFoto = document.getElementById("modal-foto");
        const modalAvatar = document.getElementById("modal-avatar");
        const modalNama = document.getElementById("modal-nama");
        const modalKelasBadge = document.getElementById("modal-kelas-badge");
        const modalNis = document.getElementById("modal-nis");
        const modalNisn = document.getElementById("modal-nisn");
        const modalJk = document.getElementById("modal-jk");
        const modalTtl = document.getElementById("modal-ttl");
        const modalAlamat = document.getElementById("modal-alamat");

        detailButtons.forEach(button => {
            button.addEventListener("click", function() {
                const nama = this.getAttribute("data-nama");
                const nis = this.getAttribute("data-nis");
                const nisn = this.getAttribute("data-nisn");
                const kelas = this.getAttribute("data-kelas");
                const jk = this.getAttribute("data-jk");
                const ttl = this.getAttribute("data-ttl");
                const alamat = this.getAttribute("data-alamat");
                const foto = this.getAttribute("data-foto");
                const huruf = this.getAttribute("data-huruf");

                modalNama.textContent = nama;
                modalKelasBadge.textContent = kelas;
                modalNis.textContent = nis;
                modalNisn.textContent = nisn;
                modalJk.textContent = jk;
                modalTtl.textContent = ttl;
                modalAlamat.textContent = alamat;

                if (foto) {
                    modalFoto.src = foto;
                    modalFotoContainer.style.display = "block";
                    modalAvatar.style.display = "none";
                } else {
                    modalAvatar.textContent = huruf;
                    modalFotoContainer.style.display = "none";
                    modalAvatar.style.display = "flex";
                }
            });
        });
    });
</script>
@endpush
