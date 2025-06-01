<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Guru;

class AbsensiController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            abort(404);
        }

        $guruId = Guru::where('user_id', auth()->id())->value('id');

        $jadwalGuru = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'mapel_id' => $item->mapel_id,
                    'kelas_id' => $item->kelas_id,
                    'hari' => $item->hari,
                    'mapel_nama' => $item->mapel->nama,
                    'kelas_nama' => $item->kelas->nama,
                    'walikelas' => $item->kelas->walikelas
                ];
            });

        $semester = Semester::where('status', 'Aktif')->first();
        $kelasWali = Kelas::where('walikelas', $guruId)->get();

        if (auth()->user()->username == 'gurupiket') {
            $kelasWali = Kelas::with('guru')->get();
        
            $jadwalGuru = Jadwal::with(['mapel', 'kelas'])->get()->map(function ($item) {
                return (object) [
                    'mapel_id' => $item->mapel_id,
                    'kelas_id' => $item->kelas_id,
                    'hari' => $item->hari,
                    'mapel_nama' => $item->mapel->nama,
                    'kelas_nama' => $item->kelas->nama,
                    'walikelas' => $item->kelas->walikelas
                ];
            });
        }
        
        return view('absensi.index', compact('jadwalGuru', 'semester', 'kelasWali'));
    }    
}