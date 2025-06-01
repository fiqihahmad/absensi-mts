<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index() {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $semesterAktif = Semester::where('status', 'aktif')->first();
            $semester = $semesterAktif ? "Semester {$semesterAktif->semester} Tahun Pelajaran {$semesterAktif->tahun_ajaran}": "Semester Yang Belum Aktif";
            if ($user->role === 'guru' && $user->guru) {
                $name = $user->guru->nama;
            } else {
                $name = $user->username;
            }
            
            return redirect()->intended('/')->with('success', "Selamat Datang, $name! Anda Login Pada $semester.");;
        }
 
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }
}
