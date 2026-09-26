<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Ekstrakurikuler;
use App\Models\GaleriFoto;
use App\Models\GaleriVideo;
use App\Models\GuruMenulis;
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
     * Menyebarkan staf administrasi/pendukung secara 2 kelompok seimbang di antara
     * kelompok guru agar tampilan card visual di beranda seimbang (contoh: 7 Guru -> 2 Staf -> 7 Guru -> 2 Staf).
     */
    private function seimbangkanTenagaPendidik(Collection $guru, Collection $staf): Collection
    {
        if ($guru->isEmpty()) {
            return $staf->values();
        }

        if ($staf->isEmpty()) {
            return $guru->values();
        }

        $jumlahGuru = $guru->count();
        $jumlahStaf = $staf->count();

        // Bagi Guru & Staf masing-masing menjadi 2 bagian seimbang
        $guruHalf = (int) ceil($jumlahGuru / 2);
        $stafHalf = (int) ceil($jumlahStaf / 2);

        $guruPart1 = $guru->slice(0, $guruHalf)->values();
        $guruPart2 = $guru->slice($guruHalf)->values();

        $stafPart1 = $staf->slice(0, $stafHalf)->values();
        $stafPart2 = $staf->slice($stafHalf)->values();

        $hasil = collect();
        foreach ($guruPart1 as $item) {
            $hasil->push($item);
        }
        foreach ($stafPart1 as $item) {
            $hasil->push($item);
        }
        foreach ($guruPart2 as $item) {
            $hasil->push($item);
        }
        foreach ($stafPart2 as $item) {
            $hasil->push($item);
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
        $gurus = GuruStaff::where('tipe', $tipe)
            ->orderBy('nama', 'asc')
            ->paginate(8)
            ->withQueryString();

        return view('pages.guru', compact('gurus', 'tipe'));
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
    public function ekstrakurikuler(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $ekstrakurikulers = Ekstrakurikuler::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('jadwal', 'like', "%{$search}%")
                        ->orWhere('pembina', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->get();

        return view('pages.ekstrakurikuler', compact('ekstrakurikulers', 'search'));
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

    /**
     * Menampilkan halaman Galeri Foto publik.
     */
    public function galeriFoto(Request $request)
    {
        $kategori = $request->query('kategori');
        $search = trim((string) $request->query('search', ''));

        $query = GaleriFoto::query();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $fotos = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $kategoriList = GaleriFoto::KATEGORI;

        return view('pages.galeri_foto', compact('fotos', 'kategoriList', 'kategori', 'search'));
    }

    /**
     * Menampilkan halaman detail foto galeri kegiatan.
     */
    public function detailGaleriFoto(GaleriFoto $galeriFoto)
    {
        $recentFotos = GaleriFoto::where('id', '!=', $galeriFoto->id)
            ->latest('tanggal')
            ->latest('id')
            ->take(4)
            ->get();

        return view('pages.detail_galeri_foto', compact('galeriFoto', 'recentFotos'));
    }

    /**
     * Menampilkan halaman Galeri Video publik.
     */
    public function galeriVideo(Request $request)
    {
        $kategori = $request->query('kategori');
        $search = trim((string) $request->query('search', ''));

        $query = GaleriVideo::query();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $videos = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $kategoriList = GaleriVideo::KATEGORI;

        return view('pages.galeri_video', compact('videos', 'kategoriList', 'kategori', 'search'));
    }

    /**
     * Menampilkan halaman informasi SPMB (Pendaftaran Siswa Baru).
     */
    public function spmb()
    {
        $spmb = ProfilSekolah::where('type', 'spmb')->first();
        $spmbData = $spmb ? $spmb->spmbParts() : (new ProfilSekolah())->spmbParts();

        return view('pages.spmb', compact('spmb', 'spmbData'));
    }

    /**
     * Menampilkan daftar karya artikel Guru Menulis.
     */
    public function guruMenulis(Request $request)
    {
        $kategori = $request->query('kategori');
        $search = trim((string) $request->query('search', ''));

        $query = GuruMenulis::query()->where('status', 'published');

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        $artikels = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(9)->withQueryString();
        $kategoriList = GuruMenulis::KATEGORI;

        return view('pages.guru_menulis', compact('artikels', 'kategoriList', 'kategori', 'search'));
    }

    /**
     * Menampilkan detail artikel Guru Menulis.
     */
    public function detailGuruMenulis(GuruMenulis $guruMenulis)
    {
        if ($guruMenulis->status !== 'published' && !auth()->check()) {
            abort(404);
        }

        $recentArtikels = GuruMenulis::where('status', 'published')
            ->where('id', '!=', $guruMenulis->id)
            ->latest('tanggal')
            ->take(4)
            ->get();

        return view('pages.detail_guru_menulis', compact('guruMenulis', 'recentArtikels'));
    }
}
