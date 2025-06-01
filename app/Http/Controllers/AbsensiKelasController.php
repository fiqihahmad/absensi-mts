<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKelas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AbsensiKelasController extends Controller
{
     public function formAbsensiKelas($kelas_id)
     {
         $guru_id = Guru::where('user_id', auth()->id())->value('id');
         
         $kelas = null;

        if (auth()->user()->username == 'gurupiket') {
            $kelas = Kelas::find($kelas_id);
        } else {
            $kelas = Kelas::where('walikelas', $guru_id)
                ->where('id', $kelas_id)
                ->first();
        }

        if (!$kelas) {
            abort(404);
        }
         $semester = Semester::where('status', 'Aktif')->first();
         $siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama')->get();
        
         return view('absensi.kelas.form', [
             'kelas' => $kelas,
             'semester' => $semester,
             'siswa' => $siswa,
             'kelas_id' => $kelas_id
         ]);
     }
     
     public function editAbsensiKelas($kelas_id)
     {
         if (auth()->user()->role == 'admin') {
             abort(404);
         }
     
         $guru_id = Guru::where('user_id', auth()->id())->value('id');
         
         $kelas = null;

        if (auth()->user()->username == 'gurupiket') {
            $kelas = Kelas::find($kelas_id);
        } else {
            $kelas = Kelas::where('walikelas', $guru_id)
                ->where('id', $kelas_id)
                ->first();
        }

        if (!$kelas) {
            abort(404);
        }
        
         $semester = Semester::where('status', 'Aktif')->first();
         $siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama')->get();
     
         $absensiData = [];
         if (request('tanggal')) {
             $absensi = AbsensiKelas::where('kelas_id', $kelas_id)
                 ->where('tanggal', request('tanggal'))
                 ->where('semester_id', $semester->id)
                 ->get();
     
             foreach ($absensi as $row) {
                 $absensiData[$row->siswa_id] = [
                     'status' => $row->status,
                     'id' => $row->id
                 ];
             }
         }
     
         return view('absensi.kelas.edit', [
             'kelas' => $kelas,
             'semester' => $semester,
             'siswa' => $siswa,
             'absensiData' => $absensiData,
             'kelas_id' => $kelas_id
         ]);
     }
     
     public function storeAbsensiKelas(Request $request)
     {
         $semester = Semester::where('status', 'Aktif')->first();
         $sudahAbsen = [];
     
         foreach ($request->siswa_id as $id_siswa) {
             $status = $request->status[$id_siswa];
             
             $exists = AbsensiKelas::where('siswa_id', $id_siswa)
                 ->where('tanggal', $request->tanggal)
                 ->where('semester_id', $semester->id)
                 ->exists();
     
             if (!$exists) {
                 AbsensiKelas::create([
                     'siswa_id' => $id_siswa,
                     'tanggal' => $request->tanggal,
                     'status' => $status,
                     'semester_id' => $semester->id,
                     'kelas_id' => $request->kelas_id
                 ]);
             } else {
                 $sudahAbsen[] = $id_siswa;
             }
         }
     
         if (count($sudahAbsen) > 0) {
             return back()->with('sudahAbsen', 'Tidak dapat melakukan absensi pada tanggal yang sama.');
         }
     
         return back()->with('tambah', 'Absensi berhasil disimpan.');
     }
     
     public function updateAbsensiKelas(Request $request)
     {
        //  $semester = Semester::where('status', 'Aktif')->first();
     
         foreach ($request->absensi as $data) {
             $absensi_id = $data['absensi_id'];
             $status = $data['status'];
             
             if ($absensi_id > 0) {
                 AbsensiKelas::where('id', $absensi_id)->update(['status' => $status]);
             }
         }
         
         return back()->with('success', 'Absensi berhasil diperbarui.');
     }

}
