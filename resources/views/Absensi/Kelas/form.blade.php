@extends('layouts.app')

@section('title', 'Form Absensi Kelas')

@section('content')
<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
      <li class="breadcrumb-item active" aria-current="page">Form Absensi</li>
    </ol>
</nav>
@if(session('tambah'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('tambah') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('sudahAbsen'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('sudahAbsen') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="d-md-flex justify-content-md-between">
            <div class="mb-md-0">
                <div class="d-flex align-items-baseline">
                    <div class="text-nowrap" style="min-width: 130px;">Kelas</div>
                    <div class="px-2">:</div>
                    <div>{{ $kelas->nama }}</div>
                </div>
            </div>
            
            <div>
                <div class="d-flex align-items-baseline">
                    <div class="text-nowrap" style="min-width: 130px;">Semester</div>
                    <div class="px-2">:</div>
                    <div>{{ $semester->semester }}</div>
                </div>
                <div class="d-flex align-items-baseline">
                    <div class="text-nowrap" style="min-width: 130px;">Th. Pelajaran</div>
                    <div class="px-2">:</div>
                    <div>{{ $semester->tahun_ajaran }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('absensi.kelas.store') }}">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
            
            <div class="row mb-3 align-items-center">
                <label for="tanggal" class="col-sm-2 col-form-label">Tanggal</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" id="tanggal" name="tanggal" 
                        value="{{ request('tanggal') ?? date('Y-m-d') }}" required>
                </div>
            </div>
        
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($siswa as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">
                            {{ $row->nama }}
                            <input type="hidden" name="siswa_id[]" value="{{ $row->id }}">
                        </td>
                        <td>
                            <select class="form-select mb-2" name="status[{{ $row->id }}]" required>
                                <option value="hadir" selected>Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" name="btnSimpanAbsensiKelas" class="btn btn-primary float-end">Simpan Absensi</button>
        </form>
    </div>
</div>
@endsection