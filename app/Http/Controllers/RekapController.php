<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKelas;
use App\Models\AbsensiMapel;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type ?? 'mapel'; // 'mapel' or 'kelas'
        $kelas_id = $request->kelas_id;
        $mapel_id = $request->mapel_id;
        $semester = Semester::where('status', 'Aktif')->first();
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $jadwal = Jadwal::with(['kelas', 'mapel'])->get();
        $kelasList = Kelas::all();
        $absensi = collect();
        $tanggalAbsensi = collect();

        // Inisialisasi default agar tidak error saat compact()
        $jml_lk = 0;
        $jml_pr = 0;
        $jml_total = 0;
        $jml_s = 0;
        $jml_i = 0;
        $jml_a = 0;
        $hari_efektif = 0;
        $presentase_hadir = 0;

        if ($kelas_id) {
            if ($type === 'mapel' && $mapel_id) {
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
            } elseif ($type === 'kelas') {
                $tanggalAbsensi = AbsensiKelas::where('kelas_id', $kelas_id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->orderBy('tanggal')
                    ->pluck('tanggal')
                    ->unique()
                    ->values();

                $absensi = AbsensiKelas::with('siswa')
                    ->where('kelas_id', $kelas_id)
                    // ->where('semester_id', $semester->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->get()
                    ->groupBy('siswa_id');
            }

            // Hanya hitung statistik jika data absensi tersedia
            if ($absensi->isNotEmpty()) {
                $siswa = $absensi->map->first()->pluck('siswa');

                $jml_lk = $siswa->where('jk', 'L')->count();
                $jml_pr = $siswa->where('jk', 'P')->count();
                $jml_total = $siswa->count();

                $jml_s = $absensi->flatMap->all()->where('status', 'sakit')->count();
                $jml_i = $absensi->flatMap->all()->where('status', 'izin')->count();
                $jml_a = $absensi->flatMap->all()->where('status', 'alpa')->count();

                $hari_efektif = $tanggalAbsensi->count();
                $total_kehadiran = $absensi->flatMap->all()->where('status', 'hadir')->count();
                $total_pertemuan = $jml_total * $hari_efektif;

                $presentase_hadir = $total_pertemuan > 0 ? round(($total_kehadiran / $total_pertemuan) * 100, 2) : 0;
            }
        }

        return view('rekap.index', compact(
            'type',
            'jadwal',
            'kelasList',
            'kelas_id',
            'mapel_id',
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
            'hari_efektif',
            'presentase_hadir'
        ));
    }
    
    function view_pdf(Request $request) {
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L', // A4 landscape
            'orientation' => 'L',
        ]);
        $type = $request->type ?? 'mapel'; // 'mapel' or 'kelas'
        $kelas_id = $request->kelas_id;
        $mapel_id = $request->mapel_id;
        $semester = Semester::where('status', 'Aktif')->first();
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $jadwal = Jadwal::with(['kelas', 'mapel'])->get();
        $kelas = Kelas::find($kelas_id);
        $absensi = collect();
        $tanggalAbsensi = collect();
    
        // Inisialisasi variabel statistik
        $jml_lk = 0;
        $jml_pr = 0;
        $jml_total = 0;
        $jml_s = 0;
        $jml_i = 0;
        $jml_a = 0;
        $hari_efektif = 0;
        $presentase_hadir = 0;
    
        if ($type === 'mapel' && $mapel_id) {
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
        } elseif ($type === 'kelas') {
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
        }
    
        // Hitung statistik jika ada data
        if ($absensi->isNotEmpty()) {
            $siswa = $absensi->map->first()->pluck('siswa');
    
            $jml_lk = $siswa->where('jk', 'L')->count();
            $jml_pr = $siswa->where('jk', 'P')->count();
            $jml_total = $siswa->count();
    
            $jml_s = $absensi->flatMap->all()->where('status', 'sakit')->count();
            $jml_i = $absensi->flatMap->all()->where('status', 'izin')->count();
            $jml_a = $absensi->flatMap->all()->where('status', 'alpa')->count();
    
            $hari_efektif = $tanggalAbsensi->count();
            $total_kehadiran = $absensi->flatMap->all()->where('status', 'hadir')->count();
            $total_pertemuan = $jml_total * $hari_efektif;
    
            $presentase_hadir = $total_pertemuan > 0 ? round(($total_kehadiran / $total_pertemuan) * 100, 2) : 0;
        }
    
        $html = view('rekap.pdf', compact(
            'type',
            'kelas',
            'mapel_id',
            'bulan',
            'tahun',
            'semester',
            'jadwal',
            'tanggalAbsensi',
            'absensi',
            'jml_lk',
            'jml_pr',
            'jml_total',
            'jml_s',
            'jml_i',
            'jml_a',
            'hari_efektif',
            'presentase_hadir'
        ))->render();
    
        $mpdf->WriteHTML($html);
        
        // Nama file PDF
        $filename = 'Rekap_Absensi_'.($type === 'mapel' ? 'Mapel' : 'Kelas').'_'.
                    $kelas->nama.'_'.date('F_Y').'.pdf';
        
        return $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }

}
