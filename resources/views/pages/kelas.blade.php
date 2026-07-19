{{--
    Halaman Direktori Kelas Publik (pages/kelas.blade.php)
    Menampilkan data kelompok kelas terdaftar beserta jurusan dan nama wali kelas masing-masing.
    Dilengkapi pencarian interaktif sisi client (JavaScript) dan paginasi jumlah baris per halaman.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Kesiswaan: Data Kelas</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <h2 class="fw-bold text-dark mb-0">Daftar Kelas</h2>
            </div>
        </div>

        <!-- Table Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Controls Row -->
                <div class="row align-items-center g-3 mb-3 directory-controls">
                    <!-- Show Entries -->
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2 directory-entries">
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
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center justify-content-sm-end gap-2 directory-search">
                            <span class="text-secondary small">Search:</span>
                            <input type="search" id="search-input" class="form-control form-control-sm border-secondary-subtle" enterkeyhint="search">
                        </div>
                    </div>
                </div>

                <!-- Simple Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center directory-table" id="table-kelas">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5" style="width: 80px;">No</th>
                                <th class="py-2.5">Kelas</th>
                                <th class="py-2.5">Jurusan</th>
                                <th class="py-2.5">Wali Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $item)
                            <tr>
                                <td class="py-2.5 td-no">{{ $item['no'] }}</td>
                                <td class="py-2.5 text-dark">{{ $item['kelas'] }}</td>
                                <td class="py-2.5 text-secondary">{{ $item['jurusan'] }}</td>
                                <td class="py-2.5 text-dark">{{ $item['wali_kelas'] }}</td>
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
@endsection

@push('styles')
<style>
    .directory-search .form-control { width: 180px; }
    .directory-table { width: 100%; min-width: 620px; font-size: 1rem; }

    @media (max-width: 575.98px) {
        .directory-search { width: 100%; }
        .directory-search .form-control { width: 100%; min-width: 0; }
        .directory-table { font-size: .875rem; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search-input");
        const showEntries = document.getElementById("show-entries");
        const tableBody = document.querySelector("#table-kelas tbody");
        
        // Find existing rows
        const originalRows = Array.from(tableBody.querySelectorAll("tr:not(.no-data-row)"));

        function filterTable() {
            const query = searchInput.value.toLowerCase().trim();
            const limit = parseInt(showEntries.value);
            
            let visibleCount = 0;
            let matchCount = 0;
            
            originalRows.forEach(row => {
                const cells = Array.from(row.querySelectorAll("td"));
                const matchesSearch = cells.some(cell => cell.textContent.toLowerCase().includes(query));
                
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

            // Handle Empty State dynamically
            const noDataRow = tableBody.querySelector(".no-data-row");
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
        
        filterTable(); // Run initially
    });
</script>
@endpush
