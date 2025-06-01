<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
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
        $semesters = Semester::all();
        return view('semester.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255',
            'semester' => 'required|in:ganjil,genap'
        ]);

        // Cek duplikat
        $exists = Semester::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->exists();

        if ($exists) {
            return redirect()->route('semester.index')->with('error', 'Gagal tambah semester, silahkan pilih semester dan tahun ajaran yang berbeda.');
        }

        Semester::create($request->all());

        return redirect()->route('semester.index')->with('tambah', 'Data berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return redirect()->route('semester.index')->with('hapus', 'Data semester berhasil dihapus.');
    }

    public function activate($id)
    {
        // Nonaktifkan semua semester terlebih dahulu
        Semester::query()->update(['status' => 'Nonaktif']);

        // Aktifkan semester yang dipilih
        $semester = Semester::findOrFail($id);
        $semester->update(['status' => 'Aktif']);

        return redirect()->route('semester.index')->with('success', 'Semester berhasil diaktifkan!');
    }

    public function deactivate($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->update(['status' => 'Nonaktif']);

        return redirect()->route('semester.index')->with('success', 'Semester berhasil dinonaktifkan!');
    }
}