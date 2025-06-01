<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class MapelController extends Controller
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
        $mapels = Mapel::all();
        return view('mapel.index', compact('mapels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mapel,nama'
        ],[
            'nama.required' => 'Mata pelajaran wajib diisi.',
            'nama.unique' => 'Mata pelajaran sudah tersedia.',
        ]);

        Mapel::create($request->all());

        return redirect()->route('mapel.index')->with('tambah', 'Data mapel berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // cek nama mapel unik
        $request->validate([
            'nama' => 'required|string|max:255|unique:mapel,nama,' . $id
        ], [
            'nama.required' => 'Mata pelajaran wajib diisi.',
            'nama.unique' => 'Gagal memperbarui, mata pelajaran sudah tersedia.',
        ]);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($request->all());

        return redirect()->route('mapel.index')->with('edit', 'Data mapel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        // Cek apakah mapel memiliki relasi dengan jadwal
        if ($mapel->jadwal()->exists()) {
            return redirect()->route('mapel.index')->with('hapus', 'Gagal menghapus mapel, silakan hapus jadwal mengajar yang terkait terlebih dahulu.');
        }

        // Jika tidak ada relasi, lanjut hapus
        $mapel->delete();

        return redirect()->route('mapel.index')->with('hapus', 'Data mapel berhasil dihapus.');
    }
}