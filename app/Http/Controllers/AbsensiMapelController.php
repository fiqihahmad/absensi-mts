<?php

namespace App\Http\Controllers;


use App\Models\AbsensiMapel;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AbsensiMapelController extends Controller
{
    public function formAbsensiMapel($mapel_id, $kelas_id)
    {
        if (auth()->user()->role == 'admin') {
            abort(404);
        }

        $guru_id = Guru::where('user_id', auth()->id())->value('id');

        $data = null;

        if (auth()->user()->username == 'gurupiket') {
            $data = Jadwal::with(['kelas', 'mapel'])
                ->where('mapel_id', $mapel_id)
                ->where('kelas_id', $kelas_id)
                ->first();
        } else {
            $data = Jadwal::with(['kelas', 'mapel'])
                ->where('guru_id', $guru_id)
                ->where('mapel_id', $mapel_id)
                ->where('kelas_id', $kelas_id)
                ->first();
        }

        if (!$data) {
            abort(404);
        }

        $semester = Semester::where('status', 'Aktif')->first();
        $siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama')->get();

        return view('absensi.mapel.form', [
            'data' => (object) [
                'mapel_nama' => $data->mapel->nama,
                'kelas_nama' => $data->kelas->nama
            ],
            'semester' => $semester,
            'siswa' => $siswa,
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id,
            'guru_id' => $guru_id
        ]);
    }

    public function editAbsensiMapel($mapel_id, $kelas_id)
    {
        if (auth()->user()->role == 'admin') {
            abort(404);
        }

        $guru_id = Guru::where('user_id', auth()->id())->value('id');

        $data = null;

        if (auth()->user()->username == 'gurupiket') {
            $data = Jadwal::with(['kelas', 'mapel'])
                ->where('mapel_id', $mapel_id)
                ->where('kelas_id', $kelas_id)
                ->first();
        } else {
            $data = Jadwal::with(['kelas', 'mapel'])
                ->where('guru_id', $guru_id)
                ->where('mapel_id', $mapel_id)
                ->where('kelas_id', $kelas_id)
                ->first();
        }

        $semester = Semester::where('status', 'Aktif')->first();
        $siswa = Siswa::where('kelas_id', $kelas_id)->get();

        $absensiData = [];
        if (request('tanggal')) {
            $absensi = AbsensiMapel::where('kelas_id', $kelas_id)
                ->where('mapel_id', $mapel_id)
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

        return view('absensi.mapel.edit', [
            'data' => (object) [
                'mapel_nama' => $data->mapel->nama,
                'kelas_nama' => $data->kelas->nama
            ],
            'semester' => $semester,
            'siswa' => $siswa,
            'absensiData' => $absensiData,
            'kelas_id' => $kelas_id,
            'mapel_id' => $mapel_id
        ]);
    }

    public function storeAbsensiMapel(Request $request)
    {
        $semester = Semester::where('status', 'Aktif')->first();
        $sudahAbsen = [];

        foreach ($request->siswa_id as $id_siswa) {
            $status = $request->status[$id_siswa];
            
            $exists = AbsensiMapel::where('siswa_id', $id_siswa)
                ->where('mapel_id', $request->mapel_id)
                ->where('kelas_id', $request->kelas_id)
                ->where('tanggal', $request->tanggal)
                ->where('semester_id', $semester->id)
                ->exists();

            if (!$exists) {
                AbsensiMapel::create([
                    'kelas_id' => $request->kelas_id,
                    'siswa_id' => $id_siswa,
                    'guru_id' => $request->guru_id,
                    'mapel_id' => $request->mapel_id,
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'semester_id' => $semester->id
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

    public function updateAbsensiMapel(Request $request)
    {
        $semester = Semester::where('status', 'Aktif')->first();

        foreach ($request->absensi as $data) {
            $absensi_id = $data['absensi_id'];
            $status = $data['status'];
            
            if ($absensi_id > 0) {
                AbsensiMapel::where('id', $absensi_id)->update(['status' => $status]);
            }
        }
        
        return back()->with('success', 'Absensi berhasil diperbarui.');
    }
}