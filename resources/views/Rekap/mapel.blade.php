@extends('layouts.app')
@section('title', 'Rekap Absensi Mapel')

<style>
    @media print {
        @page {
            margin: 5mm; 
        }
    
        body {
            margin: 0;
            padding: 0;
            color: #000 !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
            font-size: 12px;
        }

        tbody {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #000 !important; 
            color: #000 !important;             
            padding: 2px;
        }

        .sidebar,
        .navbar,
        .no-print {
            display: none !important;
        }

        #print-area {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
        
    }
    </style>
    

@section('content')
<div class="container">
    <h1 class="h3 mb-3 no-print">Rekap Absensi Mapel</h1>
    <div class="card no-print">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Pilih Kelas</label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
        
                    <div class="col-md-4 mb-3">
                        <label>Pilih Mapel</label>
                        <select name="mapel_id" class="form-control" required>
                            <option value="">Pilih Mapel</option>
                            @foreach($jadwal->groupBy('mapel_id') as $m_id => $items)
                                <option value="{{ $m_id }}" {{ request('mapel_id') == $m_id ? 'selected' : '' }}>
                                    {{ $items->first()->mapel->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
        
                    <div class="col-md-2 mb-3">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
        
                    <div class="col-md-2 mb-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="{{ $tahun }}">
                    </div>
                </div>
        
                <div class="mt-3">
                    <button class="btn btn-primary">Tampilkan Rekap</button>
                </div>
            </form>
        </div>
    </div>

    @if($absensi->count() > 0)
    <div class="card">
        <div class="card-body">
            <button class="btn btn-danger no-print" onclick="printSection('print-area')">
                <i class="fas fa-print"></i> Cetak PDF
            </button>
            <div class="table-responsive mt-4" id="print-area">
                <table class="table table-bordered">
                    <caption style="caption-side: top; padding: 1rem;" class="p-3">
                        <div class="text-center">
                            <h4 class="fw-bold">DAFTAR HADIR PESERTA DIDIK</h4>
                            <h6>MTs DARUL ISHLAH PAMULANG</h6>
                            <h6>TAHUN PELAJARAN {{ $semester->tahun_ajaran }}</h6>
                        </div>
                        <div class="d-flex justify-content-between flex-wrap text-dark">
                            <div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Mata Pelajaran</div>
                                    <div>: {{ $jadwal->firstWhere('mapel_id', $mapel_id)?->mapel->nama ?? '-' }}</div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Kelas</div>
                                    <div>: {{ $jadwal->firstWhere('kelas_id', $kelas_id)?->kelas->nama ?? '-' }}</div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Bulan</div>
                                    <div>: {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Semester</div>
                                    <div>: {{ $semester->semester }}</div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Th. Pelajaran</div>
                                    <div>: {{ $semester->tahun_ajaran }}</div>
                                    {{-- @dd($semester->tahun_ajaran) --}}
                                </div>
                            </div>
                        </div>
                    </caption>
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" rowspan="2">NO</th>
                            <th class="align-middle" rowspan="2">Nama Siswa</th>
                            <th colspan="{{ count($tanggalAbsensi) }}">Tanggal</th>
                            <th colspan="3">Jumlah</th>
                        </tr>
                        <tr>
                            @foreach($tanggalAbsensi as $tgl)
                                <th>{{ \Carbon\Carbon::parse($tgl)->format('d') }}</th>
                            @endforeach
                            <th>S</th>
                            <th>I</th>
                            <th>A</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($absensi as $index => $items)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-nowrap text-start">{{ $items->first()->siswa->nama ?? '-' }}</td>
                                @foreach($tanggalAbsensi as $tgl)
                                    @php
                                        $record = $items->firstWhere('tanggal', $tgl);
                                        $huruf = match($record->status ?? '') {
                                            'hadir' => '●',
                                            'sakit' => 'S',
                                            'izin' => 'I',
                                            'alpa' => 'A',
                                            default => '',
                                        };
                                    @endphp
                                    <td>{{ $huruf }}</td>
                                @endforeach
                                <td>{{ $items->where('status', 'sakit')->count() }}</td>
                                <td>{{ $items->where('status', 'izin')->count() }}</td>
                                <td>{{ $items->where('status', 'alpa')->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(request()->filled('kelas_id') && request()->filled('mapel_id'))
        <div class="alert alert-warning mt-4">Tidak ada data absensi pada bulan ini.</div>
    @endif
</div>

<script>
    function printSection(id) {
        var originalContents = document.body.innerHTML;
        var printContents = document.getElementById(id).innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        // Reload JS/CSS again after print
        window.location.reload();
    }
</script>


@endsection