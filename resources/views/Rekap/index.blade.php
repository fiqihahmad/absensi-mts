{{-- @extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'mapel' ? 'active' : '' }}" href="?type=mapel">Rekap Mata Pelajaran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $type === 'kelas' ? 'active' : '' }}" href="?type=kelas">Rekap Kelas</a>
                </li>
            </ul>

            <form method="GET" class="mb-3">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row">
                    <div class="col-md-4">
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
        
                    @if($type === 'mapel')
                    <div class="col-md-4">
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
                    @endif
        
                    <div class="col-md-2">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
        
                    <div class="col-md-2">
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
            <a href="{{ route('rekap.pdf', [
                'type' => $type,
                'kelas_id' => $kelas_id,
                'mapel_id' => $mapel_id,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <div class="table-responsive mt-4">
                <table class="table table-bordered text-center">
                    <caption style="caption-side: top; padding: 1rem;" class="p-3">
                        <div class="text-center">
                            <h4 class="fw-bold">DAFTAR HADIR PESERTA DIDIK</h4>
                            <h6>MTs DARUL ISHLAH PAMULANG</h6>
                            <h6>TAHUN PELAJARAN {{ $semester->tahun_ajaran }}</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($type === 'mapel' && isset($mapel_id))
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Mata Pelajaran</div>
                                    <div><strong>: {{ $jadwal->firstWhere('mapel_id', $mapel_id)?->mapel->nama ?? '-' }}</strong></div>
                                </div>
                                @endif
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Kelas</div>
                                    <div><strong>: {{ ($type === 'mapel' ? $jadwal->firstWhere('kelas_id', $kelas_id)?->kelas->nama : $kelasList->firstWhere('id', $kelas_id)?->nama) ?? '-' }}</strong></div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Bulan</div>
                                    <div><strong>: {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}</strong></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Semester</div>
                                    <div><strong>: {{ $semester->semester }}</strong></div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Th. Pelajaran</div>
                                    <div><strong>: {{ $semester->tahun_ajaran }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </caption>
                    <thead>
                        <tr>
                            <th rowspan="2">NO</th>
                            <th rowspan="2">Nama Siswa</th>
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
                                <td>{{ $items->first()->siswa->nama ?? '-' }}</td>
                                @foreach($tanggalAbsensi as $tgl)
                                    @php
                                        $record = $items->firstWhere('tanggal', $tgl);
                                        $huruf = match($record->status ?? '') {
                                            'hadir' => 'H',
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
        @if($type === 'kelas')
        <div class="card">
            <div class="card-body">
                <div class="row">
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
                                            <td>{{ $items->first()->siswa->nama ?? '-' }}</td>
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
                                        <td>{{ $jml_s + $jml_i + $jml_a }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Kelas</div>
                                    <div>: <strong>{{ $kelasList->firstWhere('id', $kelas_id)?->nama ?? '-' }}</strong></div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Bulan</div>
                                    <div>: <strong>{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }}</strong></div>
                                </div>
                                <div class="d-flex">
                                    <div style="min-width: 130px;">Hari Efektif</div>
                                    <div>: <strong>{{ $hari_efektif }} Hari</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="jmlSiswa mt-5">
                            <h6>Jumlah Peserta Didik</h6>
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
                        <div class="jmlSiswa mt-5">
                            <h6>Absensi</h6>
                            <div class="row">
                                <div class="col">
                                    <div class="border border-dark d-flex align-items-center">
                                        <h6 class="mb-0 px-2 py-1 text-wrap">S : {{ $jml_s }}</h6>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="border border-dark d-flex align-items-center">
                                        <h6 class="mb-0 px-2 py-1 text-wrap">I : {{ $jml_i }}</h6>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="border border-dark d-flex align-items-center">
                                        <h6 class="mb-0 px-2 py-1 text-wrap">A : {{ $jml_a }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 border border-dark d-flex align-items-center">
                                <h6 class="mb-0 px-2 py-1 text-wrap">Presentase Kehadiran : {{ $presentase_hadir }} %</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @endif
    </div>
    @elseif(request()->filled('kelas_id'))
        <div class="alert alert-warning mt-4">Tidak ada data absensi pada bulan ini.</div>
    @endif
</div>
@endsection --}}