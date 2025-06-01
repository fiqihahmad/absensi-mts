<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AbsensiKelas;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\Siswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get data from database
        $jumlahSiswa = Siswa::count();
        $jumlahGuru = Guru::count();
        $semester = Semester::where('status', 'Aktif')->first();
        
        $sakit = AbsensiKelas::where('status', 'sakit')
            ->whereDate('tanggal', Carbon::today())
            ->count();

        $izin = AbsensiKelas::where('status', 'izin')
            ->whereDate('tanggal', Carbon::today())
            ->count();

        $alpa = AbsensiKelas::where('status', 'alpa')
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