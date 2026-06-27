<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\GuruStaff;
use App\Models\Prestasi;
use App\Models\Ekstrakurikuler;
use App\Models\Pesan;
use App\Models\ProfilSekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $beritas = Berita::where('status', 'published')->orderBy('tanggal', 'desc')->take(4)->get();
        $profil = ProfilSekolah::where('type', 'beranda')->first();
        $gurus = GuruStaff::where('tipe', 'guru')->take(4)->get(); // Show only guru on homepage for now
        $prestasis = Prestasi::orderBy('tanggal', 'desc')->take(4)->get();
        $ekstrakurikulers = Ekstrakurikuler::take(4)->get();
        
        $countGuru = GuruStaff::count();
        $countEkstra = Ekstrakurikuler::count();
        $countPrestasi = Prestasi::count();
        
        return view('welcome', compact('beritas', 'profil', 'gurus', 'prestasis', 'ekstrakurikulers', 'countGuru', 'countEkstra', 'countPrestasi'));
    }

    public function sambutan()
    {
        $profil = ProfilSekolah::where('type', 'sambutan')->first();
        return view('pages.sambutan', compact('profil'));
    }

    public function tentang()
    {
        $profil = ProfilSekolah::where('type', 'tentang')->first();
        return view('pages.tentang', compact('profil'));
    }

    public function visiMisi()
    {
        $profil = ProfilSekolah::where('type', 'visi_misi')->first();
        return view('pages.visimisi', compact('profil'));
    }

    public function akreditasi()
    {
        $totalGuru = GuruStaff::count();
        $profil = ProfilSekolah::where('type', 'akreditasi')->first();
        return view('pages.akreditasi', compact('totalGuru', 'profil'));
    }

    public function guru(Request $request)
    {
        $tipe = $request->query('tipe', 'guru');
        $gurus = GuruStaff::where('tipe', $tipe)->orderBy('nama', 'asc')->get();
        return view('pages.guru', compact('gurus', 'tipe'));
    }

    public function siswa(Request $request)
    {
        $kelas = $request->query('kelas');
        $search = $request->query('search');

        $query = Siswa::aktif();

        if ($kelas && in_array($kelas, ['1', '2', '3', '4', '5', '6'])) {
            $query->kelas($kelas);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswas = $query->orderBy('nama', 'asc')->get();

        return view('pages.siswa', compact('siswas', 'kelas', 'search'));
    }

    public function kelas()
    {
        $classes = [];
        for ($i = 1; $i <= 6; $i++) {
            if (\App\Models\Siswa::aktif()->kelas($i)->exists()) {
                $wali = GuruStaff::where('tipe', 'guru')
                    ->where('jabatan', 'like', "%Wali Kelas {$i}%")
                    ->first();

                $classes[] = [
                    'no' => $i,
                    'kelas' => "Kelas {$i}",
                    'jurusan' => 'Umum',
                    'wali_kelas' => $wali ? $wali->nama : '-'
                ];
            }
        }

        return view('pages.kelas', compact('classes'));
    }

    public function alumni(Request $request)
    {
        $tahun = $request->query('tahun');
        
        $availableYears = Siswa::alumni()
            ->select('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        $query = Siswa::alumni();
        if ($tahun) {
            $query->where('tahun_lulus', $tahun);
        }
        $alumni = $query->orderBy('tahun_lulus', 'desc')->orderBy('nama', 'asc')->get();

        return view('pages.alumni', compact('alumni', 'availableYears', 'tahun'));
    }

    public function prestasi()
    {
        $prestasis = Prestasi::orderBy('tanggal', 'desc')->get();
        return view('pages.prestasi', compact('prestasis'));
    }

    public function ekstrakurikuler()
    {
        $ekstrakurikulers = Ekstrakurikuler::all();
        return view('pages.ekstrakurikuler', compact('ekstrakurikulers'));
    }

    public function berita()
    {
        $beritas = Berita::where('status', 'published')->orderBy('tanggal', 'desc')->paginate(9);
        return view('pages.berita', compact('beritas'));
    }

    public function detailBerita(Berita $berita)
    {
        if ($berita->status !== 'published') {
            abort(404);
        }
        return view('pages.detail_berita', compact('berita'));
    }

    public function storePesan(Request $request)
    {
        $request->validate([
            'nama' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'pesan' => 'required|string'
        ]);

        $data = $request->all();
        $data['isi'] = $data['pesan']; // Map pesan to isi
        unset($data['pesan']);
        
        // Handle Anonim
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
