@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Daftar Jadwal Pelajaran</h1>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                {{-- Pesan dari controller 'with()' --}}
                @if(session('tambah'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('tambah') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('edit'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('edit') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('hapus'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('hapus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#tambahJadwal">
                        Tambah Jadwal
                    </button>
                </div>
                <div class="card-body table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals as $jadwal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td>{{ $jadwal->kelas->nama }}</td>
                                <td>{{ $jadwal->mapel->nama }}</td>
                                <td>{{ $jadwal->guru->nama }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <a class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editJadwal{{ $jadwal->id }}">Edit</a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $jadwal->id }}">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Modal Konfirmasi -->
                            <div class="modal fade" id="confirmDelete{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus data jadwal ini?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Jadwal -->
    <div class="modal fade" id="tambahJadwal" tabindex="-1" aria-labelledby="tambahJadwalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahJadwalLabel">Tambah Jadwal Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jadwal.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="hari" class="form-label">Hari</label>
                                    <select class="form-select" name="hari" required>
                                        <option value="" selected disabled>Pilih Hari</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                            <option value="{{ $hari }}">{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select class="form-select" name="kelas_id" required>
                                        <option value="" selected disabled>Pilih Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                                    <select class="form-select" name="mapel_id" required>
                                        <option value="" selected disabled>Pilih Mata Pelajaran</option>
                                        @foreach($mapels as $mapel)
                                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="guru_id" class="form-label">Guru</label>
                                    <select class="form-select" name="guru_id" required>
                                        <option value="" selected disabled>Pilih Guru</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Jadwal -->
    @foreach($jadwals as $jadwal)
    <div class="modal fade" id="editJadwal{{ $jadwal->id }}" tabindex="-1" aria-labelledby="editJadwalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJadwalLabel">Edit Jadwal Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jadwal.update', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <input type="hidden" name="id" value="{{ $jadwal->id }}">
                                <div class="mb-3">
                                    <label for="hari" class="form-label">Hari</label>
                                    <select class="form-select" name="hari" required>
                                        <option value="" disabled>Pilih Hari</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                            <option value="{{ $hari }}" {{ $jadwal->hari == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select class="form-select" name="kelas_id" required>
                                        <option value="" disabled>Pilih Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ $jadwal->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                                    <select class="form-select" name="mapel_id" required>
                                        <option value="" disabled>Pilih Mata Pelajaran</option>
                                        @foreach($mapels as $mapel)
                                            <option value="{{ $mapel->id }}" {{ $jadwal->mapel_id == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="guru_id" class="form-label">Guru</label>
                                    <select class="form-select" name="guru_id" required>
                                        <option value="" disabled>Pilih Guru</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}" {{ $jadwal->guru_id == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection