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
        // validasi form login
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $semesterAktif = Semester::where('status', 'aktif')->first();
            $semester = $semesterAktif ? "Semester {$semesterAktif->semester} Tahun Pelajaran {$semesterAktif->tahun_ajaran}": "Semester Yang Belum Aktif";
            
            
            return redirect()->intended('/')->with('success', "Login berhasil, semester saat ini $semester.");;
        }
 
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }
}
