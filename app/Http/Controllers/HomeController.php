<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Ekstrakurikuler;
use App\Models\GuruStaff;
use App\Models\Kelas;
use App\Models\Pesan;
use App\Models\Prestasi;
use App\Models\ProfilSekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Controller HomeController
 * 
 * Mengelola seluruh rute halaman publik (frontend user) sekolah,
 * seperti Beranda, Sambutan, Tentang, Visi & Misi, Akreditasi, daftar Guru,
 * data Siswa aktif, info alumni (Tracer Study), daftar Prestasi, Ekstrakurikuler,
 * artikel Berita, serta pengiriman Pesan/masukan dari pengunjung.
 */
class HomeController extends Controller
{
    /**
     * Menampilkan halaman Beranda (Homepage) sekolah dengan data ringkasan dinamis.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil berita terbaru yang telah dipublikasikan
        $beritas = Berita::where('status', 'published')->orderBy('tanggal', 'desc')->take(4)->get();
        $tentang = ProfilSekolah::where('type', 'tentang')->first();
        $sambutan = ProfilSekolah::where('type', 'sambutan')->first();
        
        $guru = GuruStaff::where('tipe', 'guru')->orderBy('nama')->get();
        $staf = GuruStaff::where('tipe', 'staf')->orderBy('nama')->get();
        // Menyeimbangkan penyebaran guru & staf secara proporsional untuk ditampilkan di carousel
        $tenagaPendidik = $this->seimbangkanTenagaPendidik($guru, $staf);

        $prestasis = Prestasi::orderBy('tanggal', 'desc')->take(4)->get();
        $ekstrakurikulers = Ekstrakurikuler::take(4)->get();

        // Hitung total data untuk statistik sekolah di beranda
        $countTenagaPendidik = $tenagaPendidik->count();
        $countPesertaDidik = Siswa::aktif()->count();
        $countEkstra = Ekstrakurikuler::count();
        $countPrestasi = Prestasi::count();

        return view('welcome', compact('beritas', 'tentang', 'sambutan', 'tenagaPendidik', 'prestasis', 'ekstrakurikulers', 'countTenagaPendidik', 'countPesertaDidik', 'countEkstra', 'countPrestasi'));
    }

    /**
     * Menyebarkan staf administrasi/pendukung secara merata/proporsional di antara
     * baris/daftar guru agar tampilan card visual seimbang dan rapi.
     */
    private function seimbangkanTenagaPendidik(Collection $guru, Collection $staf): Collection
    {
        if ($guru->isEmpty()) {
            return $staf->values();
        }

        if ($staf->isEmpty()) {
            return $guru->values();
        }

        $hasil = collect();
        $jumlahGuru = $guru->count();
        $jumlahStaf = $staf->count();
        $stafPerPosisi = [];

        // Hitung posisi penempatan staf di sela-sela guru
        foreach ($staf->values() as $index => $item) {
            $posisi = (int) round((($index + 1) * $jumlahGuru) / ($jumlahStaf + 1));
            $posisi = max(1, min($jumlahGuru - 1, $posisi));
            $stafPerPosisi[$posisi][] = $item;
        }

        // Susun daftar gabungan
        foreach ($guru->values() as $index => $item) {
            $hasil->push($item);
            $posisi = $index + 1;

            foreach ($stafPerPosisi[$posisi] ?? [] as $itemStaf) {
                $hasil->push($itemStaf);
            }
        }

        return $hasil;
    }

    /**
     * Menampilkan halaman Sambutan Kepala Sekolah.
     */
    public function sambutan()
    {
        $profil = ProfilSekolah::where('type', 'sambutan')->first();

        return view('pages.sambutan', compact('profil'));
    }

    /**
     * Menampilkan halaman Tentang Sekolah.
     */
    public function tentang()
    {
        $profil = ProfilSekolah::where('type', 'tentang')->first();

        return view('pages.tentang', compact('profil'));
    }

    /**
     * Menampilkan halaman Visi & Misi Sekolah.
     */
    public function visiMisi()
    {
        $profil = ProfilSekolah::where('type', 'visi_misi')->first();

        return view('pages.visimisi', compact('profil'));
    }

    /**
     * Menampilkan halaman Informasi Akreditasi Sekolah.
     */
    public function akreditasi()
    {
        $totalGuru = GuruStaff::count();
        $profil = ProfilSekolah::where('type', 'akreditasi')->first();

        return view('pages.akreditasi', compact('totalGuru', 'profil'));
    }

    /**
     * Menampilkan halaman daftar struktural Guru / Staf publik.
     */
    public function guru(Request $request)
    {
        $tipe = $request->query('tipe', 'guru');
        if (! in_array($tipe, ['guru', 'staf'], true)) {
            $tipe = 'guru';
        }
        $gurus = GuruStaff::where('tipe', $tipe)->orderBy('nama', 'asc')->get();

        return view('pages.guru', compact('gurus', 'tipe'));
    }

    /**
     * Menampilkan daftar Siswa aktif sekolah dengan pencarian dan filter kelas.
     */
    public function siswa(Request $request)
    {
        $kelas = $request->query('kelas');
        $search = $request->query('search');

        $query = Siswa::aktif()->with([
            'prestasis' => fn ($query) => $query->orderBy('tanggal', 'desc'),
            'ekstrakurikulers' => fn ($query) => $query->orderBy('nama'),
        ]);

        if ($kelas && Kelas::where('tingkat', $kelas)->exists()) {
            $query->kelas($kelas);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswas = $query->orderBy('nama', 'asc')->get();

        return view('pages.siswa', compact('siswas', 'kelas', 'search'));
    }

    /**
     * Menampilkan halaman daftar kelas dan Wali Kelas.
     */
    public function kelas()
    {
        $classes = Kelas::with('waliKelas')
            ->orderByRaw('urutan IS NULL')
            ->orderBy('urutan')
            ->orderBy('tingkat')
            ->get()
            ->map(fn (Kelas $kelas, int $index) => [
                'no' => $index + 1,
                'filter' => $kelas->tingkat,
                'kelas' => $kelas->tingkat,
                'jurusan' => $kelas->jurusan ?: '-',
                'wali_kelas' => $kelas->waliKelas?->nama ?? '-',
            ]);

        return view('pages.kelas', compact('classes'));
    }

    /**
     * Menampilkan halaman Tracer Study / Alumni beserta riwayat pendidikan/pekerjaan lanjutan.
     */
    public function alumni(Request $request)
    {
        $tahun = $request->query('tahun');

        // Mendapatkan daftar tahun kelulusan unik
        $availableYears = Siswa::alumni()
            ->select('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        $query = Siswa::alumni()->with([
            'riwayatPendidikan',
            'riwayatPekerjaan',
            'prestasis' => fn ($query) => $query->orderBy('tanggal', 'desc'),
        ]);
        if ($tahun) {
            $query->where('tahun_lulus', $tahun);
        }
        $alumni = $query->orderBy('tahun_lulus', 'desc')->orderBy('nama', 'asc')->get();

        return view('pages.alumni', compact('alumni', 'availableYears', 'tahun'));
    }

    /**
     * Menampilkan halaman daftar Prestasi yang diraih siswa.
     */
    public function prestasi()
    {
        $prestasisPerKategori = Prestasi::orderBy('tanggal', 'desc')
            ->get()
            ->groupBy('kategori');
        $kategoriPrestasi = Prestasi::KATEGORI;

        return view('pages.prestasi', compact('prestasisPerKategori', 'kategoriPrestasi'));
    }

    /**
     * Menampilkan halaman daftar Ekstrakurikuler sekolah.
     */
    public function ekstrakurikuler()
    {
        $ekstrakurikulers = Ekstrakurikuler::all();

        return view('pages.ekstrakurikuler', compact('ekstrakurikulers'));
    }

    /**
     * Menampilkan halaman daftar Berita/Pengumuman dengan pagination.
     */
    public function berita(Request $request)
    {
        $search = $request->query('search');

        $query = Berita::where('status', 'published')->orderBy('tanggal', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        $beritas = $query->paginate(6)->withQueryString();

        return view('pages.berita', compact('beritas', 'search'));
    }

    /**
     * Menampilkan halaman detail isi Berita.
     */
    public function detailBerita(Berita $berita)
    {
        // Cegah akses jika berita masih berstatus draf, kecuali jika diakses oleh admin yang terautentikasi
        if ($berita->status !== 'published' && !auth()->check()) {
            abort(404);
        }

        return view('pages.detail_berita', compact('berita'));
    }

    /**
     * Menyimpan Pesan, saran, atau masukan dari pengunjung web.
     * Mendukung opsi Anonim jika nama/email dikosongkan.
     */
    public function storePesan(Request $request)
    {
        $request->validate([
            'nama' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'pesan' => 'required|string',
        ]);

        $data = $request->only(['nama', 'email', 'pesan']);
        $data['isi'] = $data['pesan']; 
        unset($data['pesan']);

        // Jika form kosong, tandai sebagai anonim secara otomatis
        if (empty($data['nama'])) {
            $data['nama'] = '*Anonim*';
        }
        if (empty($data['email'])) {
            $data['email'] = 'anonim@rahasia.com';
        }

        Pesan::create($data);

        return redirect()->back()->with('success_pesan', 'Pesan / Masukan Anda berhasil dikirim secara anonim atau teridentifikasi!');
    }
}
