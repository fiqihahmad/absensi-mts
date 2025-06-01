@extends('layouts.app')

@section('title', 'Siswa')

@section('content')
<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Daftar Siswa</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
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
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#tambahSiswa">
                        Tambah Siswa
                    </button>
                    <button class="btn btn-info mt-2" data-bs-toggle="modal" data-bs-target="#pindahKelasModal">
                        Pindah Kelas
                    </button>                    
                    <button class="btn btn-danger mt-2" data-bs-toggle="modal" data-bs-target="#hapusKelasModal">
                        Hapus Kelas
                    </button>
                </div>
                <div class="card-body">
                    <table id="myTable" class="table responsive">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $siswa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama }}</td>
                                <td>{{ $siswa->jk }}</td>
                                <td>{{ $siswa->kelas->nama }}</td>
                                <td>
                                    <a class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSiswa{{ $siswa->id }}">Edit</a>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete{{ $siswa->id }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Kelas -->
    <div class="modal fade" id="hapusKelasModal" tabindex="-1" aria-labelledby="hapusKelasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hapusKelasModalLabel">Hapus Seluruh Siswa dalam Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.hapus-kelas') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kelas_hapus" class="form-label">Pilih Kelas yang Akan Dihapus</label>
                            <select class="form-select" name="kelas_hapus" id="kelas_hapus" required>
                                <option value="" selected disabled>Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> PERINGATAN: Semua siswa dan data absensi dalam kelas ini akan dihapus permanen!
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="konfirmasiHapus" required>
                            <label class="form-check-label" for="konfirmasiHapus">
                                Saya mengerti dan ingin melanjutkan
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pindah Kelas -->
    <div class="modal fade" id="pindahKelasModal" tabindex="-1" aria-labelledby="pindahKelasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pindahKelasModalLabel">Pindah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.pindah-kelas') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kelas_asal" class="form-label">Kelas Asal</label>
                            <select class="form-select" name="kelas_asal" id="kelas_asal" required>
                                <option value="" selected disabled>Pilih Kelas Asal</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kelas_tujuan" class="form-label">Kelas Tujuan</label>
                            <select class="form-select" name="kelas_tujuan" id="kelas_tujuan" required>
                                <option value="" selected disabled>Pilih Kelas Tujuan</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Semua siswa dari kelas asal akan dipindahkan ke kelas tujuan!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Pindahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Validasi client-side untuk memastikan kelas asal dan tujuan berbeda
            $('form').submit(function(e) {
                if ($('#kelas_asal').val() === $('#kelas_tujuan').val()) {
                    e.preventDefault();
                    alert('Kelas asal dan tujuan tidak boleh sama!');
                    return false;
                }
                return true;
            });
        });
    </script>
    @endpush

    <!-- Modal Tambah Siswa -->
    <div class="modal fade" id="tambahSiswa" tabindex="-1" aria-labelledby="tambahSiswaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSiswaLabel">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="number" class="form-control" name="nis" placeholder="930230232" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" placeholder="Budi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jk" id="lk" value="L" checked>
                                <label class="form-check-label" for="lk">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jk" id="pr" value="P">
                                <label class="form-check-label" for="pr">Perempuan</label>
                            </div>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Siswa -->
    @foreach($siswas as $siswa)
    <div class="modal fade" id="editSiswa{{ $siswa->id }}" tabindex="-1" aria-labelledby="editSiswaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSiswaLabel">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="number" class="form-control" name="nis" value="{{ $siswa->nis }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" value="{{ $siswa->nama }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jk" id="editLk{{ $siswa->id }}" value="L" {{ $siswa->jk == 'L' ? 'checked' : '' }}>
                                <label class="form-check-label" for="editLk{{ $siswa->id }}">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jk" id="editPr{{ $siswa->id }}" value="P" {{ $siswa->jk == 'P' ? 'checked' : '' }}>
                                <label class="form-check-label" for="editPr{{ $siswa->id }}">Perempuan</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select class="form-select" name="kelas_id" required>
                                <option value="" disabled>Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
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
    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmDelete{{ $siswa->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data siswa <strong>{{ $siswa->nama }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection