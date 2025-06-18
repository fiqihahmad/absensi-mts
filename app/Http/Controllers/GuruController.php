<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class GuruController extends Controller
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
        // ambil semua data guru dan data user
        $gurus = Guru::with('user')->get();
        
        return view('guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        // validasi form input
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => [
                'nullable',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
        ], [
            'username.unique' => 'Username sudah ada, silahkan pilih username lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed' => 'Password harus berisi minimal satu huruf besar dan satu huruf kecil.',
            'password.numbers' => 'Password harus berisi minimal satu angka.'
        ]);

        // Buat user guru disimpan di tabel users
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'guru'
        ]);

        // Simpa data guru di tabel guru
        Guru::create([
            'user_id' => $user->id,
            'nama' => $request->nama
        ]);

        return redirect()->route('guru.index')->with('tambah', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // ambil id guru
        $guru = Guru::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$guru->user_id
        ]);

        // ambil data username di tabel users
        $userData = [
            'username' => $request->username
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // update data username di tabel user
        $guru->user->update($userData);

        // Update data nama di tabel guru
        $guru->update([
            'nama' => $request->nama
        ]);

        return redirect()->route('guru.index')->with('edit', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // ambil id guru yang akan dihapus
        $guru = Guru::with('user')->findOrFail($id);

        // Jika guru masih menjadi wali kelas gagal hapus guru
        if ($guru->kelas()->count() > 0) {
            return redirect()->route('guru.index')->with('error', 'Gagal menghapus guru karena masih menjadi walikelas, silahkan ubah walikelas terlebih dahulu di menu kelas.');
        }
        
        // Jika guru masih ada di tabel jadwal gagal hapus guru
        if ($guru->jadwal()->count() > 0) {
            return redirect()->route('guru.index')->with('error', 'Gagal menghapus guru karena masih terdapat jadwal pelajaran.');
        }

        // Hapus data guru di tabel user
        $guru->user->delete();
        
        // Hapus data guru di tabel guru
        $guru->delete();

        return redirect()->route('guru.index')->with('hapus', 'Data guru berhasil dihapus.');
    }
}