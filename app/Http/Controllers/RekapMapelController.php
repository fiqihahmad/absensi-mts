<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiMapel;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;

class RekapMapelController extends Controller
{

public function index(Request $request)
{
    $kelas_id = $request->kelas_id;
    $mapel_id = $request->mapel_id;
    $bulan = $request->bulan ?? date('m');
    $tahun = $request->tahun ?? date('Y');

    $jadwal = Jadwal::with(['kelas', 'mapel'])->get();
    $kelasList = Kelas::all();
    $absensi = collect();
    $tanggalAbsensi = collect();
    $semester = null;

    if ($kelas_id && $mapel_id) {
        // Ambil semester dari absensi_mapel untuk ditampilkan di tampilan rekap
        $semester = AbsensiMapel::with('semester')->where('kelas_id', $kelas_id)
            ->where('mapel_id', $mapel_id)->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)->first()?->semester;

        $tanggalAbsensi = AbsensiMapel::where('kelas_id', $kelas_id)
            ->where('mapel_id', $mapel_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->unique()
            ->values();

        $absensi = AbsensiMapel::with('siswa')
            ->where('kelas_id', $kelas_id)
            ->where('mapel_id', $mapel_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->groupBy('siswa_id');
    }

    return view('rekap.mapel', compact(
        'jadwal',
        'kelasList',
        'kelas_id',
        'mapel_id',
        'bulan',
        'semester',
        'tahun',
        'tanggalAbsensi',
        'absensi'
    ));
}

}