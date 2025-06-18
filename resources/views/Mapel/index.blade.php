@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Daftar Mata Pelajaran</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">

                {{-- Pesan dari controller 'with()' --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
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
                
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#tambahMapel">
                        Tambah Mata Pelajaran
                    </button>
                </div>
                <div class="card-body table-responsive">
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mapels as $mapel)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mapel->nama }}</td>
                                <td>
                                    <div class="d-inline-flex gap-1">
                                        <a class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editMapel{{ $mapel->id }}">Edit</a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $mapel->id }}">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Modal Konfirmasi -->
                            <div class="modal fade" id="confirmDelete{{ $mapel->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah Anda yakin ingin menghapus data mapel <strong>{{ $mapel->nama }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('mapel.destroy', $mapel->id) }}" method="POST" class="d-inline">
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

    <!-- Modal Tambah Mapel -->
    <div class="modal fade" id="tambahMapel" tabindex="-1" aria-labelledby="tambahMapelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahMapelLabel">Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('mapel.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Mata Pelajaran</label>
                            <input type="text" class="form-control" name="nama" required>
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

    <!-- Modal Edit Mapel -->
    @foreach($mapels as $mapel)
    <div class="modal fade" id="editMapel{{ $mapel->id }}" tabindex="-1" aria-labelledby="editMapelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMapelLabel">Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('mapel.update', $mapel->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $mapel->id }}">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Mata Pelajaran</label>
                            <input type="text" class="form-control" name="nama" value="{{ $mapel->nama }}" required>
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