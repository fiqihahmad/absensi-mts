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
        $gurus = Guru::with('user')->get();
        return view('guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
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

        // Buat user baru
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'guru'
        ]);

        // Buat data guru
        Guru::create([
            'user_id' => $user->id,
            'nama' => $request->nama
        ]);

        return redirect()->route('guru.index')->with('tambah', 'Data guru berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$guru->user_id
        ]);

        // Update data user
        $userData = [
            'username' => $request->username
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $guru->user->update($userData);

        // Update data guru
        $guru->update([
            'nama' => $request->nama
        ]);

        return redirect()->route('guru.index')->with('edit', 'Data guru berhasil diperbarui');
    }

    public function destroy($id)
    {
        $guru = Guru::with('user')->findOrFail($id);

        // Cek apakah guru masih menjadi wali kelas
        if ($guru->kelas()->count() > 0) {
            return redirect()->route('guru.index')->with('error', 'Gagal menghapus guru karena masih menjadi walikelas, silahkan ubah walikelas terlebih dahulu di menu kelas.');
        }

        if ($guru->jadwal()->count() > 0) {
            return redirect()->route('guru.index')->with('error', 'Gagal menghapus guru karena masih terdapat jadwal mengajar.');
        }

        // Hapus user terkait
        $guru->user->delete();
        
        // Hapus guru
        $guru->delete();

        return redirect()->route('guru.index')->with('hapus', 'Data guru berhasil dihapus');
    }
}