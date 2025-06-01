@extends('layouts.app')

@section('title', 'Semester')

@section('content')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Daftar Semester</h1>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                @if(session('tambah'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('tambah') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('success') }}
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
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#tambahSemester">
                        Tambah Semester
                    </button>
                </div>
                <div class="card-body text-center">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Semester</th>
                                <th>Tahun Ajaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semesters as $semester)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ ucfirst($semester->semester) }}</td>
                                <td>{{ $semester->tahun_ajaran }}</td>
                                <td>
                                    @if($semester->status == 'Aktif')
                                        <span class="bg-success text-sm text-white px-3 py-1 rounded-pill border-0">Aktif</span>
                                    @else
                                        <span class="bg-warning text-sm text-white px-3 py-1 rounded-pill border-0">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($semester->status == 'Nonaktif')
                                        <a href="{{ route('semester.activate', $semester->id) }}" class="btn btn-sm btn-outline-danger">Aktifkan</a>
                                    @else
                                        <a href="{{ route('semester.deactivate', $semester->id) }}" class="btn btn-sm btn-outline-danger">Nonaktifkan</a>
                                    @endif
                                    {{-- <form action="{{ route('semester.destroy', $semester->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin mau hapus?')">Hapus</button>
                                    </form> --}}
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $semester->id }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <!-- Modal Konfirmasi -->
                            <div class="modal fade" id="confirmDelete{{ $semester->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="text-danger">Menghapus semester dapat mengakibatkan seluruh data absensi semester yang dipilih terhapus, pastikan semua guru sudah mengunduh data absensi di menu rekap absensi. </h5>
                                            Apakah Anda yakin ingin menghapus semester <strong>{{ $semester->semester }} {{ $semester->tahun_ajaran }}</strong> ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('semester.destroy', $semester->id) }}" method="POST" class="d-inline">
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

    <!-- Modal Tambah Semester -->
    <div class="modal fade" id="tambahSemester" tabindex="-1" aria-labelledby="tambahSemesterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSemesterLabel">Tambah Semester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('semester.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" name="tahun_ajaran" placeholder="2025/2026" required>
                        </div>
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" name="semester" required>
                                <option value="" selected disabled>Pilih Semester</option>
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
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
</div>
@endsection