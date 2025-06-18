<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\RekapController;
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
    
    Route::get('/absensi/form/{kelas_id}/{mapel_id?}', [AbsensiController::class, 'form'])->name('absensi.form');
    Route::get('/absensi/edit/{kelas_id}/{mapel_id?}', [AbsensiController::class, 'edit'])->name('absensi.edit');
    Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::post('/absensi/update', [AbsensiController::class, 'update'])->name('absensi.update');


    // Rekap Absensi
    Route::prefix('rekap')->group(function () {
        Route::get('/mapel', [RekapController::class, 'mapel'])->name('rekap.mapel');
        Route::get('/kelas', [RekapController::class, 'kelas'])->name('rekap.kelas');
        Route::get('/pdf', [RekapController::class, 'pdf'])->name('rekap.pdf'); // Tetap pakai controller utama untuk PDF
    });




});