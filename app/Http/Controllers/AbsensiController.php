<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Guru;
use App\Models\Siswa;

class AbsensiController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'admin') {
            abort(404);
        }

        // ambil id guru yang login
        $guruId = Guru::where('user_id', auth()->id())->value('id');

        // ambil data jadwal
        $jadwalGuru = Jadwal::with(['kelas', 'mapel'])->where('guru_id', $guruId)->get();
        
        // ambil data semester aktif
        $semester = Semester::where('status', 'Aktif')->first();

        // ambil data walikelas
        $kelasWali = Kelas::where('walikelas', $guruId)->get();

        // jika yang login gurupiket tampilkan data absensi kelas dan absensi mapel
        if (auth()->user()->username == 'gurupiket') {
            $kelasWali = Kelas::with('guru')->get();
        
            $jadwalGuru = Jadwal::with(['mapel', 'kelas'])->get();
        }
        
        return view('absensi.index', compact('jadwalGuru', 'semester', 'kelasWali'));
    }    

    // form tambah absensi
    public function form($kelas_id, $mapel_id = null)
    {
        // ambil id guru yang login
        $guru_id = Guru::where('user_id', auth()->id())->value('id');

        // ambil semester aktif
        $semester = Semester::where('status', 'Aktif')->first();

        // jika semester tidak aktif redirect ke halaman 404
        if (!$semester) abort(404, 'Semester aktif tidak ditemukan.');

        // ambil id kelas dari form input
        $kelas = Kelas::findOrFail($kelas_id);

        // ambil data siswa berdasarkan kelas
        $siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama')->get();

        if ($mapel_id) {
            // ambil id kelas dan id mapel dari jadwal
            $jadwal = Jadwal::with(['mapel'])->where('kelas_id', $kelas_id)->where('mapel_id', $mapel_id);

            // jika user yang login bukan guru piket dan bukan guru yang sesuai jadwal tampil halaman 404
            if (auth()->user()->username !== 'gurupiket') {
                $jadwal->where('guru_id', $guru_id);
            }
            $jadwal = $jadwal->firstOrFail();

            return view('absensi.mapel.form', [
                'data' => (object)[
                    'mapel_nama' => $jadwal->mapel->nama,
                    'kelas_nama' => $kelas->nama
                ],
                'semester' => $semester,
                'siswa' => $siswa,
                'kelas_id' => $kelas_id,
                'mapel_id' => $mapel_id,
                'guru_id' => $guru_id
            ]);
        }

        // jika user yang login bukan guru piket dan bukan walikelas saat ini tampil halaman 404
        if (auth()->user()->username !== 'gurupiket') {
            $kelas = Kelas::where('walikelas', $guru_id)->where('id', $kelas_id)->firstOrFail();
        }

        return view('absensi.kelas.form', compact('kelas', 'semester', 'siswa', 'kelas_id'));
    }

    public function edit($kelas_id, $mapel_id = null)
    {
        // ambil id guru yang login
        $guru_id = Guru::where('user_id', auth()->id())->value('id');

        // ambil semester aktif
        $semester = Semester::where('status', 'Aktif')->first();

        if (!$semester) abort(404);

        // ambil data siswa berdasarkan kelas
        $siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama')->get();

        $absensiData = [];

        // ambil data absensi kelas
        $query = Absensi::where('kelas_id', $kelas_id)
            ->where('tanggal', request('tanggal'))
            ->where('semester_id', $semester->id);

        if ($mapel_id) {
            // Ambil data jadwal berdasarkan kelas dan mapel
            $jadwal = Jadwal::with(['mapel', 'kelas'])->where('kelas_id', $kelas_id)->where('mapel_id', $mapel_id);

            // jika user yang login bukan guru piket dan bukan guru sesuai jadwal tampil halaman 404
            if (auth()->user()->username !== 'gurupiket') {
                $jadwal->where('guru_id', $guru_id);
            }
            $jadwal = $jadwal->firstOrFail();

            // ambil data absensi mapel
            $query->where('mapel_id', $mapel_id);

            // Buat objek untuk menampilkan nama mapel dan kelas di form edit absensi.
            $data = (object)[
                'mapel_nama' => $jadwal->mapel->nama,
                'kelas_nama' => $jadwal->kelas->nama
            ];
        } else {
            // jika user yang login bukan guru piket dan bukan walikelas saat ini tampil halaman 404
            if (auth()->user()->username !== 'gurupiket') {
                $kelas = Kelas::where('walikelas', $guru_id)->where('id', $kelas_id)->firstOrFail();
            } else {
                $kelas = Kelas::findOrFail($kelas_id);
            }
            // Buat objek untuk menampilkan nama kelas di form edit absensi.
            $kelas = Kelas::findOrFail($kelas_id);
            $data = (object)[
                'mapel_nama' => null,
                'kelas_nama' => $kelas->nama
            ];
            $query->whereNull('mapel_id');
        }

        // tampung data absensi
        $absensi = $query->get();

        // looping data absensi dan tampung ke absensiData
        foreach ($absensi as $row) {
            $absensiData[$row->siswa_id] = [
                'status' => $row->status,
                'id' => $row->id
            ];
        }

        return view($mapel_id ? 'absensi.mapel.edit' : 'absensi.kelas.edit', compact(
            'data', 'semester', 'siswa', 'absensiData', 'kelas_id', 'mapel_id'
        ));
    }

    public function store(Request $request)
    {
        $semester = Semester::where('status', 'Aktif')->first();
        $sudahAbsen = [];

        // Looping id siswa dan ambil status dari form
        foreach ($request->siswa_id as $id_siswa) {
            $status = $request->status[$id_siswa];

            // cek absen di database
            $absenTersedia = Absensi::where('siswa_id', $id_siswa)
                ->where('tanggal', $request->tanggal)
                ->where('semester_id', $semester->id)
                ->where('kelas_id', $request->kelas_id)
                ->when($request->mapel_id, fn($q) => $q->where('mapel_id', $request->mapel_id))
                ->when(!$request->mapel_id, fn($q) => $q->whereNull('mapel_id'))
                ->exists();

            // input absen jika belum ada absensi
            if (!$absenTersedia) {
                Absensi::create([
                    'siswa_id' => $id_siswa,
                    'kelas_id' => $request->kelas_id,
                    'guru_id' => $request->guru_id ?? null,
                    'mapel_id' => $request->mapel_id ?? null,
                    'tanggal' => $request->tanggal,
                    'status' => $status,
                    'semester_id' => $semester->id
                ]);
            } else {
                $sudahAbsen[] = $id_siswa;
            }
        }

        if ($sudahAbsen) {
            return back()->with('sudahAbsen', 'Tidak dapat melakukan absensi pada tanggal yang sama.');
        }

        return back()->with('tambah', 'Absensi berhasil disimpan.');
    }
    
    public function update(Request $request)
    {
        // looping data dari form edit absensi
        foreach ($request->absensi as $siswaId => $data) {
            $absensi_id = $data['absensi_id'];
            $status = $data['status'];
        
            if ($absensi_id > 0) {
                Absensi::where('id', $absensi_id)->update(['status' => $status]);
            }
        }
        

        return back()->with('success', 'Absensi berhasil diperbarui.');
    }
}