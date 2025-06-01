<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiKelasController;
use App\Http\Controllers\AbsensiMapelController;
use App\Http\Controllers\AMController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RekapKelasController;
use App\Http\Controllers\RekapMapelController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::resource('kelas', KelasController::class);
Route::resource('guru', GuruController::class)->middleware('auth');
Route::resource('siswa', SiswaController::class)->middleware('auth');
Route::post('/siswa/pindah-kelas', [SiswaController::class, 'pindahKelas'])->name('siswa.pindah-kelas');
Route::post('/siswa/hapus-kelas', [SiswaController::class, 'hapusKelas'])->name('siswa.hapus-kelas');
Route::resource('mapel', MapelController::class)->middleware('auth');
Route::resource('jadwal', JadwalController::class)->middleware('auth');

Route::resource('semester', SemesterController::class)->middleware('auth');
Route::get('/semester/aktif/{id}', [SemesterController::class, 'activate'])->name('semester.activate')->middleware('auth');
Route::get('/semester/nonaktif/{id}', [SemesterController::class, 'deactivate'])->name('semester.deactivate')->middleware('auth');

Route::prefix('profil')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::put('/', [UserController::class, 'update'])->name('user.update');
});

// Absensi Routes
Route::middleware(['auth'])->group(function () {
    // Absensi Routes
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    
    // Absensi Mapel
    Route::get('/absensi/input/{mapel_id}/{kelas_id}', [AbsensiMapelController::class, 'formAbsensiMapel'])
        ->name('absensi.mapel.input');
    Route::get('/absensi/data/{mapel_id}/{kelas_id}', [AbsensiMapelController::class, 'editAbsensiMapel'])
        ->name('absensi.mapel.data');
    Route::post('/absensi/mapel', [AbsensiMapelController::class, 'storeAbsensiMapel'])
        ->name('absensi.mapel.store');
    Route::post('/absensi/mapel/update', [AbsensiMapelController::class, 'updateAbsensiMapel'])
        ->name('absensi.mapel.update');
    
    
    Route::get('/absensi/kelas/input/{kelas_id}', [AbsensiKelasController::class, 'formAbsensiKelas'])->name('absensi.kelas.input'); 
    Route::get('/absensi/kelas/data/{kelas_id}', [AbsensiKelasController::class, 'editAbsensiKelas'])->name('absensi.kelas.edit'); 
    Route::post('/absensi/kelas', [AbsensiKelasController::class, 'storeAbsensiKelas'])->name('absensi.kelas.store'); 
    Route::post('/absensi/kelas/update', [AbsensiKelasController::class, 'updateAbsensiKelas'])->name('absensi.kelas.update'); 

    // Rekap Absensi
    Route::prefix('rekap')->group(function () {
        Route::get('/mapel', [RekapMapelController::class, 'index'])->name('rekap.mapel');
        Route::get('/kelas', [RekapKelasController::class, 'index'])->name('rekap.kelas');
        Route::get('/pdf', [RekapController::class, 'pdf'])->name('rekap.pdf'); // Tetap pakai controller utama untuk PDF
    });




});