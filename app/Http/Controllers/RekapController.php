<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    // rekap absensi kelas
    public function kelas(Request $request)
    {
        // Mengambil data kelas_id, bulan, dan tahun dari form
        $kelas_id = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        // Ambil semua data kelas dan guru
        $kelasList = Kelas::all();
        $guruList = Guru::all();
        $absensi = collect();
        $tanggalAbsensi = collect();
        $semester = null;

        // variabel jumlah rekap
        $jml_lk = $jml_pr = $jml_total = 0;
        $jml_s = $jml_i = $jml_a = $jml_absensi = 0;
        $presentase_sakit = $presentase_izin = $presentase_alpa = $presentase_absensi = 0;
        $hari_efektif = $presentase_hadir = 0;

        if ($kelas_id) {
            // Ambil data semester
            $semester = Absensi::with('semester')
                ->where('kelas_id', $kelas_id)
                ->whereNull('mapel_id')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->first()?->semester;

            // Ambil tanggal absensi
            $tanggalAbsensi = Absensi::where('kelas_id', $kelas_id)
                ->whereNull('mapel_id')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal')
                ->pluck('tanggal')
                ->unique()
                ->values();

            // Ambil seluruh data absensi kelas
            $absensi = Absensi::with('siswa')
                ->where('kelas_id', $kelas_id)
                ->whereNull('mapel_id')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get()
                ->groupBy('siswa_id');

            // ambil data absensi jika datanya ada
            if ($absensi->isNotEmpty()) {
                $siswa = $absensi->map->first()->pluck('siswa');

                // Hitung jumlah siswa berdasarkan jenis kelamin
                $jml_lk = $siswa->where('jk', 'L')->count();
                $jml_pr = $siswa->where('jk', 'P')->count();
                $jml_total = $siswa->count();

                // Hitung jumlah status sakit, izin, alpa
                $jml_s = $absensi->flatMap->all()->where('status', 'sakit')->count();
                $jml_i = $absensi->flatMap->all()->where('status', 'izin')->count();
                $jml_a = $absensi->flatMap->all()->where('status', 'alpa')->count();
                $jml_absensi = $jml_s + $jml_i + $jml_a;

                // Hitung presentase kehadiran dan ketidakhadiran
                $hari_efektif = $tanggalAbsensi->count();
                if ($hari_efektif > 0 && $jml_total > 0) {
                    $presentase_sakit = floor(($jml_s * 10000) / ($jml_total * $hari_efektif)) / 100;
                    $presentase_izin = floor(($jml_i * 10000) / ($jml_total * $hari_efektif)) / 100;
                    $presentase_alpa = floor(($jml_a * 10000) / ($jml_total * $hari_efektif)) / 100;
                    $presentase_absensi = $presentase_sakit + $presentase_izin + $presentase_alpa;
                    $presentase_hadir = 100 - $presentase_absensi;
                }
            }
        }

        return view('rekap.kelas', compact(
            'kelasList', 'kelas_id', 'guruList', 'bulan', 'tahun', 'semester',
            'tanggalAbsensi', 'absensi',
            'jml_lk', 'jml_pr', 'jml_total',
            'jml_s', 'jml_i', 'jml_a', 'jml_absensi',
            'presentase_sakit', 'presentase_izin', 'presentase_alpa',
            'presentase_absensi', 'hari_efektif', 'presentase_hadir'
        ));
    }

    // rekap absensi mapel
    public function mapel(Request $request)
    {
        // Mengambil data kelas_id, mapel_id, bulan, dan tahun dari form
        $kelas_id = $request->kelas_id;
        $mapel_id = $request->mapel_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        // ambil data kelas dan jadwal
        $kelasList = Kelas::all();
        $jadwal = Jadwal::with(['kelas', 'mapel'])->get();
        $absensi = collect();
        $tanggalAbsensi = collect();
        $semester = null;

        if ($kelas_id && $mapel_id) {
            // ambil data semester
            $semester = Absensi::with('semester')
                ->where('kelas_id', $kelas_id)
                ->where('mapel_id', $mapel_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->first()?->semester;

            // ambil tanggal absensi
            $tanggalAbsensi = Absensi::where('kelas_id', $kelas_id)
                ->where('mapel_id', $mapel_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal')
                ->pluck('tanggal')
                ->unique()
                ->values();

            // ambil data absensi
            $absensi = Absensi::with('siswa')
                ->where('kelas_id', $kelas_id)
                ->where('mapel_id', $mapel_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get()
                ->groupBy('siswa_id');
        }

        return view('rekap.mapel', compact(
            'jadwal', 'kelasList', 'kelas_id', 'mapel_id', 'bulan', 'tahun', 'semester',
            'tanggalAbsensi', 'absensi'
        ));
    }    
}
