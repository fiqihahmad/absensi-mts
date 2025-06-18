@extends('layouts.app')

@section('title', 'Data Absensi')

@section('content')
<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}">Absensi</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit Absensi</li>
    </ol>
</nav>
<div class="container-fluid p-0">
   
{{-- Pesan dari controller 'with()' --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

    <div class="card">
        <div class="card-body">
            <div class="d-md-flex justify-content-md-between">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-baseline">
                        <div class="text-nowrap" style="min-width: 130px;">Mata Pelajaran</div>
                        <div class="px-2">:</div>
                        <div>{{ $data->mapel_nama }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="text-nowrap" style="min-width: 130px;">Kelas</div>
                        <div class="px-2">:</div>
                        <div>{{ $data->kelas_nama }}</div>
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
    
            <!-- Form Pilih Tanggal -->
            <form method="GET" action="">
                <input type="hidden" name="mapel_id" value="{{ $mapel_id }}">
                <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
    
                <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                    <label for="tanggal" class="col-form-label flex-shrink-0" style="width: 80px">Tanggal</label>
                    <input type="date" 
                        name="tanggal" 
                        id="tanggal" 
                        class="form-control flex-grow-1"
                        value="{{ request('tanggal') ?? date('Y-m-d') }}" 
                        required>
                    <button type="submit" class="btn btn-primary flex-shrink-0">
                        <span class="">Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Absensi -->
    @if(!empty($absensiData))
        <div class="card">
            <div class="card-body">
                <form action="{{ route('absensi.update') }}" method="POST">
                    @csrf
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
                                @php
                                    $status = $absensiData[$row->id]['status'] ?? 'hadir';
                                    $absen_id = $absensiData[$row->id]['id'] ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start">
                                        {{ $row->nama }}
                                        <input type="hidden" name="absensi[{{ $row->id }}][absensi_id]" value="{{ $absen_id }}">
                                        <input type="hidden" name="absensi[{{ $row->id }}][siswa_id]" value="{{ $row->id }}">
                                    </td>
                                    <td>
                                        <select class="form-select" name="absensi[{{ $row->id }}][status]" required>
                                            <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="alpa" {{ $status == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Hidden input tambahan -->
                    <input type="hidden" name="mapel_id" value="{{ $mapel_id }}">
                    <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
                    <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">

                    <button type="submit" name="btnEditAbsensi" class="btn btn-primary float-end">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    @elseif(request('tanggal'))
        <div class="alert alert-warning">Tidak ada data absensi untuk tanggal tersebut.</div>
    @endif
</div>
@endsection