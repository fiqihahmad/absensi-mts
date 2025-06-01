<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiKelas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Semester;

class RekapKelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas_id = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $semester = null;

        $kelasList = Kelas::all();
        $guruList = Guru::all();
        $absensi = collect();
        $tanggalAbsensi = collect();

        // Inisialisasi default
        $jml_lk = 0;
        $jml_pr = 0;
        $jml_total = 0;
        $jml_s = 0;
        $jml_i = 0;
        $jml_a = 0;
        $jml_absensi = 0;
        $presentase_sakit = 0;
        $presentase_izin = 0;
        $presentase_alpa = 0;
        $presentase_absensi = 0;
        $hari_efektif = 0;
        $presentase_hadir = 0;

        if ($kelas_id) {
            $semester = AbsensiKelas::with('semester')->where('kelas_id', $kelas_id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)->first()?->semester;

            $tanggalAbsensi = AbsensiKelas::where('kelas_id', $kelas_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal')
                ->pluck('tanggal')
                ->unique()
                ->values();

            $absensi = AbsensiKelas::with('siswa')
                ->where('kelas_id', $kelas_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get()
                ->groupBy('siswa_id');

            if ($absensi->isNotEmpty()) {
                $siswa = $absensi->map->first()->pluck('siswa');

                $jml_lk = $siswa->where('jk', 'L')->count();
                $jml_pr = $siswa->where('jk', 'P')->count();
                $jml_total = $siswa->count();

                $jml_s = $absensi->flatMap->all()->where('status', 'sakit')->count();
                $jml_i = $absensi->flatMap->all()->where('status', 'izin')->count();
                $jml_a = $absensi->flatMap->all()->where('status', 'alpa')->count();
                $jml_absensi = $jml_s + $jml_i + $jml_a;

                $hari_efektif = $tanggalAbsensi->count();
                $presentase_sakit = ($jml_s * 100) / ($jml_total * $hari_efektif);
                $presentase_sakit = floor($presentase_sakit * 100) / 100;
                $presentase_izin = ($jml_i * 100) / ($jml_total * $hari_efektif);
                $presentase_izin = floor($presentase_izin * 100) / 100;
                $presentase_alpa = ($jml_a * 100) / ($jml_total * $hari_efektif);
                $presentase_alpa = floor($presentase_alpa * 100) / 100;
                $presentase_absensi = $presentase_sakit + $presentase_izin + $presentase_alpa;

                $total_pertemuan = $jml_total * $hari_efektif;

                $presentase_hadir = (100 - $presentase_absensi);
            }
        }

        return view('rekap.kelas', compact(
            'kelasList',
            'kelas_id',
            'bulan',
            'semester',
            'tahun',
            'tanggalAbsensi',
            'absensi',
            'jml_lk',
            'jml_pr',
            'jml_total',
            'jml_s',
            'jml_i',
            'jml_a',
            'jml_absensi',
            'presentase_sakit',
            'presentase_izin',
            'presentase_alpa',
            'presentase_absensi',
            'hari_efektif',
            'presentase_hadir',
            'guruList',
        ));
    }
}