<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\Siswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil jumlah data dari database

        $jumlahSiswa = Siswa::count();
        $jumlahGuru = Guru::count();
        $semester = Semester::where('status', 'Aktif')->first();
        
        // ambil data jumlah siswa sakit hari ini
        $sakit = Absensi::whereNull('mapel_id')
            ->where('status', 'sakit')
            ->whereDate('tanggal', Carbon::today())
            ->count();

        // ambil data jumlah siswa izin hari ini
        $izin = Absensi::whereNull('mapel_id')
            ->where('status', 'izin')
            ->whereDate('tanggal', Carbon::today())
            ->count();

        // ambil data jumlah siswa alpa hari ini
        $alpa = Absensi::whereNull('mapel_id')
            ->where('status', 'alpa')
            ->whereDate('tanggal', Carbon::today())
            ->count();
        
        return view('dashboard.index', [
            'jumlahSiswa' => $jumlahSiswa,
            'jumlahGuru' => $jumlahGuru,
            'semester' => $semester,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
        ]);
    }
}