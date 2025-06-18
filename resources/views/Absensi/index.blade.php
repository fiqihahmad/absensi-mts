@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="container-fluid p-0">
    
    @if(!$semester)
        <div class="alert alert-warning">
            <h4>Semester Tidak Aktif</h4>
            <p>Hubungi admin/tata usaha untuk mengaktifkan semester.</p>
        </div>
    @else
        <div class="cardJadwal">
            @foreach(['tambah', 'edit', 'error'] as $type)
                @if(session($type))
                    <div class="alert alert-{{ $type === 'error' ? 'danger' : 'success' }}">
                        {{ session($type) }}
                    </div>
                @endif
            @endforeach
            
            
            <!-- Card Absensi Kelas -->
            @if($kelasWali->count() > 0)
                <div class="mb-4">
                    <h4 class="h3 mb-3">Absensi Kelas</h4>
                    <div class="row justify-content-start">
                        @foreach($kelasWali as $kelas)
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-lg border-0 rounded-4 h-100">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-bold text-dark text-center">
                                                Kelas {{ $kelas->nama }}
                                            </h5>
                                            <p class="text-center">Walikelas : {{ $kelas->guru->nama }}</p>
                                        </div>
                                        <div class="d-flex justify-content-center gap-2 mt-3">
                                            <a href="{{ route('absensi.form', ['kelas_id' => $kelas->id]) }}" class="btn btn-primary btn-md rounded-3 ">
                                                <i class="align-middle" data-feather="book-open"></i></i> Isi Absensi
                                            </a>
                                            <a href="{{ route('absensi.edit', ['kelas_id' => $kelas->id]) }}" class="btn btn-outline-primary btn-md rounded-3 ">
                                                <i class="align-middle" data-feather="edit"></i> Edit Absensi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Card Absensi Berdasarkan Jadwal Mapel -->
            <div class="mt-4">
                <h4 class="h3 mb-3">Absensi Mata Pelajaran</h4>
                <div class="row">
                    @foreach($jadwalGuru as $row)
                    {{-- @dd($row) --}}
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-lg border-0 rounded-4 h-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title fw-bold text-dark text-center">
                                            {{ $row->mapel->nama }}
                                        </h5>
                                        <ul class="list-unstyled mb-3">
                                            <li class="d-flex">
                                                <span class="me-2" style="width: 60px;">Kelas</span>
                                                <span>: {{ $row->kelas->nama }}</span>
                                            </li>
                                            <li class="d-flex">
                                                <span class="me-2" style="width: 60px;">Hari</span>
                                                <span>: {{ $row->hari }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('absensi.form', ['mapel_id' => $row->mapel_id, 'kelas_id' => $row->kelas_id]) }}" class="btn btn-primary btn-md rounded-3">
                                            <i class="align-middle" data-feather="book-open"></i> Isi Absensi
                                        </a>
                                        <a href="{{ route('absensi.edit', ['mapel_id' => $row->mapel_id, 'kelas_id' => $row->kelas_id]) }}" class="btn btn-outline-primary btn-md rounded-3">
                                            <i class="align-middle" data-feather="edit"></i> Edit Absensi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection