<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sambutan', [HomeController::class, 'sambutan'])->name('sambutan');
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');
Route::get('/visi-misi', [HomeController::class, 'visiMisi'])->name('visi-misi');
Route::get('/akreditasi', [HomeController::class, 'akreditasi'])->name('akreditasi');
Route::get('/guru', [HomeController::class, 'guru'])->name('guru');
Route::get('/prestasi', [HomeController::class, 'prestasi'])->name('prestasi');
Route::get('/ekstrakurikuler', [HomeController::class, 'ekstrakurikuler'])->name('ekstrakurikuler');
Route::get('/siswa', [HomeController::class, 'siswa'])->name('siswa');
Route::get('/kelas', [HomeController::class, 'kelas'])->name('kelas');
Route::get('/alumni', [HomeController::class, 'alumni'])->name('alumni');
Route::get('/berita', [HomeController::class, 'berita'])->name('berita');
Route::get('/berita/{berita}', [HomeController::class, 'detailBerita'])->name('berita.detail');
Route::post('/pesan', [HomeController::class, 'storePesan'])->name('pesan.store');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'admin', 'admin.idle'])
    ->name('dashboard');

Route::middleware(['auth', 'admin', 'admin.idle'])->group(function () {
    Route::post('admin/ping', function () {
        return response()->json(['status' => 'ok']);
    })->name('admin.ping');

    Route::get('admin/akun', [AdminAccountController::class, 'edit'])->name('admin.account.edit');
    Route::put('admin/akun', [AdminAccountController::class, 'update'])->name('admin.account.update');

    Route::resource('admin/berita', \App\Http\Controllers\BeritaController::class)
        ->except('show')
        ->names('admin.berita')
        ->parameters(['berita' => 'berita']);
    Route::resource('admin/guru-staff', \App\Http\Controllers\GuruStaffController::class)
        ->except('show')
        ->names('admin.guru-staff')
        ->parameters(['guru-staff' => 'guruStaff']);
    Route::resource('admin/prestasi', \App\Http\Controllers\PrestasiController::class)->except('show')->names('admin.prestasi');
    Route::resource('admin/ekstrakurikuler', \App\Http\Controllers\EkstrakurikulerController::class)->except('show')->names('admin.ekstrakurikuler');
    Route::get('admin/pesan', [\App\Http\Controllers\PesanController::class, 'index'])->name('admin.pesan.index');
    Route::patch('admin/pesan/{pesan}/baca', [\App\Http\Controllers\PesanController::class, 'markAsRead'])->name('admin.pesan.read');
    Route::delete('admin/pesan/{pesan}', [\App\Http\Controllers\PesanController::class, 'destroy'])->name('admin.pesan.destroy');
    Route::get('admin/aktivitas', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.activities.index');
    
    Route::get('admin/profil/{type}', [\App\Http\Controllers\ProfilSekolahController::class, 'editByType'])->name('admin.profil-sekolah.editType');
    Route::put('admin/profil/{type}', [\App\Http\Controllers\ProfilSekolahController::class, 'updateByType'])->name('admin.profil-sekolah.updateType');
    
    Route::get('admin/pengaturan', [\App\Http\Controllers\SettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('admin/pengaturan', [\App\Http\Controllers\SettingController::class, 'update'])->name('admin.settings.update');
    
    Route::resource('admin/users', \App\Http\Controllers\UserController::class)->except('show')->names('admin.users');
    
    Route::get('admin/siswa-kenaikan-kelas', [\App\Http\Controllers\SiswaController::class, 'promotePage'])->name('admin.siswa.promote.page');
    Route::post('admin/siswa-kenaikan-kelas', [\App\Http\Controllers\SiswaController::class, 'promote'])->name('admin.siswa.promote');
    Route::patch('admin/siswa/{id}/pulihkan', [\App\Http\Controllers\SiswaController::class, 'restore'])->name('admin.siswa.restore');
    Route::get('admin/siswa-ekspor', [\App\Http\Controllers\SiswaController::class, 'export'])->name('admin.siswa.export');
    Route::resource('admin/kelas', \App\Http\Controllers\KelasController::class)
        ->except('show')
        ->names('admin.kelas')
        ->parameters(['kelas' => 'kelas']);
    Route::resource('admin/siswa', \App\Http\Controllers\SiswaController::class)->except('show')->names('admin.siswa');
});

require __DIR__.'/auth.php';
