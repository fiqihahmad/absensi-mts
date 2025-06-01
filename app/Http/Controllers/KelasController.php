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
        $kelas = Kelas::with('guru')->get();
        $gurus = Guru::all();
        return view('kelas.index', compact('kelas', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|unique:kelas,id',
            'nama' => 'required|unique:kelas,nama',
            'walikelas' => 'required|exists:guru,id'
        ]);

        Kelas::create([
            'id' => $request->id,
            'nama' => $request->nama,
            'walikelas' => $request->walikelas
        ]);

        return redirect()->route('kelas.index')->with('tambah', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id' => 'required|unique:kelas,id,'.$id,
            'nama' => 'required|unique:kelas,nama,'.$id,
            'walikelas' => 'required|exists:guru,id'
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update([
            'id' => $request->id,
            'nama' => $request->nama,
            'walikelas' => $request->walikelas
        ]);

        return redirect()->route('kelas.index')->with('edit', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        
        if ($kelas->siswa()->count() > 0) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh data siswa. Silahkan hapus terlebih dahulu siswa dengan kelas tersebut.');
        }
        if ($kelas->jadwal()->count() > 0) {
            return redirect()->route('kelas.index')->with('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh data jadwal. Silahkan hapus terlebih dahulu jadwal dengan kelas tersebut.');
        }

        $kelas->delete();
        return redirect()->route('kelas.index')->with('hapus', 'Data kelas berhasil dihapus.');
    }
}