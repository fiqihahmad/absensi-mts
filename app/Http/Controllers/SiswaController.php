<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;

class SiswaController extends Controller
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
        // Ambil semua data siswa secara urut berdasarkan kelas dan nama
        $siswas = Siswa::with('kelas')->orderBy('kelas_id')->orderBy('nama')->get();

        $kelas = Kelas::all();
        return view('siswa.index', compact('siswas', 'kelas'));
    }

    public function store(Request $request)
    {
        // validasi form input
        $request->validate([
            'nis' => 'required|numeric|unique:siswa,nis',
            'nama' => 'required|string|max:255',
            'jk' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id'
        ], [
            'nis.unique' => 'Gagal tambah siswa, NIS sudah terdaftar!',
            'nis.required' => 'NIS wajib diisi!',
        ]);

        // tambah siswa
        Siswa::create($request->all());

        return redirect()->route('siswa.index')->with('tambah', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nis' => 'required|numeric|unique:siswa,nis,'.$id,
            'nama' => 'required|string|max:255',
            'jk' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        // cari id siswa yang diedit
        $siswa = Siswa::findOrFail($id);

        // ubah siswa
        $siswa->update($request->all());

        return redirect()->route('siswa.index')->with('edit', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // ambil id siswa yang akan dihapus
        $siswa = Siswa::findOrFail($id);

        // jika semester statusnya aktif tidak dapat hapus siswa
        if (Semester::where('status', 'Aktif')->exists()) {
            return redirect()->route('siswa.index')->with('error', 
                'Tidak dapat menghapus siswa saat semester aktif.');
        }

        // hapus siswa
        $siswa->delete();
        
        return redirect()->route('siswa.index')->with('hapus', 'Data siswa berhasil dihapus.');
    }

    public function pindahKelas(Request $request)
    {
        $request->validate([
            'kelas_asal' => 'required|exists:kelas,id',
            'kelas_tujuan' => 'required|exists:kelas,id|different:kelas_asal'
        ]);

        // Update kelas untuk semua siswa di kelas asal
        $jumlahSiswa = Siswa::where('kelas_id', $request->kelas_asal)->update(['kelas_id' => $request->kelas_tujuan]);

        if ($jumlahSiswa > 0) {
            return redirect()->route('siswa.index')->with('edit', $jumlahSiswa . ' siswa berhasil dipindahkan ke kelas baru.');
        }

        return redirect()->route('siswa.index')->with('error', 'Tidak ada siswa di kelas yang dipilih.');
    }

    public function hapusKelas(Request $request)
    {
        $request->validate([
            'kelas_hapus' => 'required|exists:kelas,id'
        ]);

        // Cek apakah ada siswa di kelas tersebut
        $jumlahSiswa = Siswa::where('kelas_id', $request->kelas_hapus)->count();

        if ($jumlahSiswa == 0) {
            return redirect()->route('siswa.index')->with('error', 'Tidak ada siswa di kelas yang dipilih.');
        }

        // Hapus semua siswa di kelas tersebut
        $jumlahSiswaTerhapus = Siswa::where('kelas_id', $request->kelas_hapus)->delete();

        return redirect()->route('siswa.index')->with('hapus', $jumlahSiswaTerhapus . ' siswa berhasil dihapus dari kelas.');
    }
}