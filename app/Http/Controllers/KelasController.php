<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
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
        // ambil semua data kelas
        $kelas = Kelas::all();

        // ambil semua data guru
        $gurus = Guru::all();

        return view('kelas.index', compact('kelas', 'gurus'));
    }

    public function store(Request $request)
    {
        // validasi form input
        $request->validate([
            'id' => 'required|unique:kelas,id',
            'nama' => 'required|unique:kelas,nama',
            'walikelas' => 'required|unique:kelas,walikelas|exists:guru,id'
        ]);

        // tambah kelas
        Kelas::create($request->all());

        return redirect()->route('kelas.index')->with('tambah', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // validasi form input
        $request->validate([
            'nama' => 'required|unique:kelas,nama,' . $id,
            'walikelas' => 'required|unique:kelas,walikelas,' . $id . ',id|exists:guru,id'
        ]);

        // ambil id kelas yang akan diubah
        $kelas = Kelas::findOrFail($id);

        // ubah kelas
        $kelas->update($request->all());

        return redirect()->route('kelas.index')->with('edit', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // ambil id kelas yang akan dihapus
        $kelas = Kelas::findOrFail($id);
        
        // Jika ada data siswa saat kelas dihapus maka gagal hapus kelas
        if ($kelas->siswa()->count() > 0) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh data siswa. Silahkan hapus terlebih dahulu siswa dengan kelas tersebut.');
        }
        // Jika ada data jadwal saat kelas dihapus maka gagal hapus kelas
        if ($kelas->jadwal()->count() > 0) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh data jadwal. Silahkan hapus terlebih dahulu jadwal dengan kelas tersebut.');
        }

        // hapus kelas
        $kelas->delete();
        
        return redirect()->route('kelas.index')->with('hapus', 'Data kelas berhasil dihapus.');
    }
}