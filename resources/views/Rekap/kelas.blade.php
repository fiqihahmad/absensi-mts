@extends('layouts.app')
@section('title', 'Rekap Absensi Kelas')

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
    
    #print-area, #print-area-1 {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .row {
        display: flex !important;
        flex-wrap: nowrap !important;
    }

    .col-md-8, .col-md-4 {
        float: left !important;
        display: block !important;
    }

    .col-md-8 {
        width: 66.2% !important; 
        margin-right: 1%; 
    }


    .col-md-4 {
        width: 32.5% !important;
    }

}
</style>

@section('content')
<div class="container">
    <h1 class="h3 mb-3 no-print">Rekap Absensi Kelas</h1>
    <p>cek</p>
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
                <table class="table table-bordered text-center">
                    <caption style="caption-side: top; padding: 1rem;" class="p-3">
                        <div class="text-center">
                            <h4 class="fw-bold">DAFTAR HADIR PESERTA DIDIK</h4>
                            <h6>MTs DARUL ISHLAH PAMULANG</h6>
                            <h6>TAHUN PELAJARAN {{ $semester->tahun_ajaran }}</h6>
                        </div>
                        <div class="d-flex justify-content-between flex-wrap text-dark">
                            <div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Kelas</div>
                                    <div>: {{ $kelasList->firstWhere('id', $kelas_id)?->nama ?? '-' }}</div>
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
                                </div>
                            </div>
                        </div>
                    </caption>
                    <thead>
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
                    <tbody>
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
        <hr>
        <div class="card-body">
            <button class="btn btn-danger no-print" onclick="printSection('print-area-1')">
                <i class="fas fa-print"></i> Cetak PDF
            </button>
            <div class="row mt-4" id="print-area-1">
                <div class="col-12 col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">NO</th>
                                    <th class="text-center align-middle" rowspan="2">NAMA SISWA</th>
                                    <th class="text-center align-middle" rowspan="2">JK</th>
                                    <th colspan="3">KETERANGAN</th>
                                    <th class="text-center align-middle" rowspan="2">JML</th>
                                </tr>
                                <tr>
                                    <th>S</th>
                                    <th>I</th>
                                    <th>A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($absensi as $index => $items)
                                    @php
                                        $sakit = $items->where('status', 'sakit')->count();
                                        $izin = $items->where('status', 'izin')->count();
                                        $alpa = $items->where('status', 'alpa')->count();
                                        $total = $sakit + $izin + $alpa;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $items->first()->siswa->nama ?? '-' }}</td>
                                        <td>{{ $items->first()->siswa->jk ?? '-' }}</td>
                                        <td>{{ $sakit }}</td>
                                        <td>{{ $izin }}</td>
                                        <td>{{ $alpa }}</td>
                                        <td>{{ $total }}</td>
                                    </tr>
                                @endforeach
    
                                <tr class="fw-bold">
                                    <td colspan="3">TOTAL</td>
                                    <td>{{ $jml_s }}</td>
                                    <td>{{ $jml_i }}</td>
                                    <td>{{ $jml_a }}</td>
                                    <td>{{ $jml_absensi }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-between">
                        <div class="text-dark">
                            <div class="d-flex">
                                <div style="min-width: 130px;">Kelas</div>
                                <div>: {{ $kelasList->firstWhere('id', $kelas_id)?->nama ?? '-' }}</div>
                            </div>
                            <div class="d-flex">
                                <div style="min-width: 130px;">Bulan</div>
                                <div>: {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}</div>
                            </div>
                            <div class="d-flex">
                                <div style="min-width: 130px;">Hari Efektif</div>
                                <div>: {{ $hari_efektif }} Hari</div>
                            </div>
                        </div>
                    </div>
                    <div class="jmlSiswa mt-4">
                        <h6>Rekapitulasi Jumlah dan Absensi</h6>
                        <h6>1. Jumlah Peserta Didik</h6>
                        <div class="row row-cols-1 row-cols-sm-3">
                            <div class="col">
                                <div class="border border-dark d-flex align-items-center">
                                    <h6 class="mb-0 px-2 py-1 text-wrap">Lk : {{ $jml_lk }}</h6>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border border-dark d-flex align-items-center">
                                    <h6 class="mb-0 px-2 py-1 text-wrap">Pr : {{ $jml_pr }}</h6>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border border-dark d-flex align-items-center">
                                    <h6 class="mb-0 px-2 py-1 text-wrap">Jml : {{ $jml_total }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="jmlSiswa mt-4">
                        <h6>2. Absensi</h6>
                        <div class="row">
                            <div class="col">
                                <div class="border border-dark d-flex align-items-center">
                                    <h6 class="mb-0 px-2 py-1 text-wrap">S : {{ $presentase_sakit }}</h6>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border border-dark d-flex align-items-center">
                                    <h6 class="mb-0 px-2 py-1 text-wrap">I : {{ $presentase_izin }}</h6>
                                </div>
                            </div>
                            <div class="col">
                                <div class="border border-dark d-flex align-items-center">
                                    <h6 class="mb-0 px-2 py-1 text-wrap">A : {{ $presentase_alpa }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 border border-dark d-flex align-items-center">
                            <h6 class="mb-0 px-2 py-1 text-wrap">Jml : {{ $presentase_absensi }} %</h6>
                        </div>
                        <div class="mt-3 border border-dark d-flex align-items-center">
                            <h6 class="mb-0 px-2 py-1 text-wrap">Jml. Presentase Kehadiran : {{ $presentase_hadir }} %</h6>
                        </div>
                        <div class="mt-3">
                            {{-- {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }} --}}
                            <h6>Tangsel, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');}} </h6>
                            <h6>Wali Kelas</h6>
                            @php
                                $idGuru = $kelasList->firstWhere('id', $kelas_id)?->walikelas;
                                $namaGuru = $guruList->firstWhere('id', $idGuru)?->nama;
                            @endphp
                            <h6 class="mt-6"><strong>{{ $namaGuru }}</strong></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    @elseif(request()->filled('kelas_id'))
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