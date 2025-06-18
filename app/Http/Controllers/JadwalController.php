<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;
use App\Models\Semester;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role == 'guru') {
                abort(404);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $jadwals = Jadwal::with(['kelas', 'mapel', 'guru'])->get();
        $kelas = Kelas::all();
        $mapels = Mapel::all();
        $gurus = Guru::all();
        
        return view('jadwal.index', compact('jadwals', 'kelas', 'mapels', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'guru_id' => 'required|exists:guru,id'
        ]);

        // tambah jadwal
        Jadwal::create($request->all());

        return redirect()->route('jadwal.index')->with('tambah', 'Data jadwal pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // validasi form input
        $request->validate([
            'hari' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'guru_id' => 'required|exists:guru,id'
        ]);

        // cari id jadwal yang akan diubah
        $jadwal = Jadwal::findOrFail($id);

        // ubah jadwal
        $jadwal->update($request->all());

        return redirect()->route('jadwal.index')->with('edit', 'Data jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Cek apakah ada semester aktif
        if (Semester::where('status', 'Aktif')->exists()) {
            return redirect()->route('jadwal.index')->with('error', 'Tidak dapat menghapus jadwal pelajaran saat semester aktif.');
        }
        // cari id jadwal yang akan dihapus
        $jadwal = Jadwal::findOrFail($id);

        // hapus jadwal
        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('hapus', 'Data jadwal pelajaran berhasil dihapus.');
    }
}