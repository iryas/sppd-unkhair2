@extends('layouts.backend')

@section('content')
    @php
        $namaBln = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $maxBln = max(1, max($spd_bulanan));
    @endphp

    <div>
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard <small class="text-muted">Admin SPD</small></h1>
                    </div>
                    <div class="col-sm-6 text-sm-right">
                        <a href="{{ route('admin.sppd.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Buat SPPD
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content pl-2 pr-2">
            <div class="container-fluid">

                <!-- Kartu ringkasan status -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $spd_diajukan }}</h3>
                                <p>SPPD Diajukan {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-import"></i></div>
                            <a href="{{ route('admin.sppd.index') }}" class="small-box-footer">Lihat semua <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $spd_disetujui }}</h3>
                                <p>Disetujui</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                            <a href="{{ route('admin.sppd.index') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $spd_menunggu }}</h3>
                                <p>Menunggu Review</p>
                            </div>
                            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                            <a href="{{ route('admin.sppd.index') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $spd_dibatalkan }}</h3>
                                <p>Dibatalkan</p>
                            </div>
                            <div class="icon"><i class="fas fa-ban"></i></div>
                            <a href="{{ route('admin.sppd.review') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Grafik SPPD per bulan -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-bar"></i> SPPD Disetujui per Bulan
                                    {{ $tahun }}</h3>
                            </div>
                            <div class="card-body">
                                <div style="display:flex;align-items:flex-end;gap:5px;height:170px;">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;">
                                            <small style="font-size:10px;color:#666;">{{ $spd_bulanan[$m] }}</small>
                                            <div title="{{ $namaBln[$m] }}: {{ $spd_bulanan[$m] }}"
                                                style="width:100%;background:#012970;border-radius:3px 3px 0 0;height:{{ round(($spd_bulanan[$m] / $maxBln) * 130) }}px;min-height:2px;">
                                            </div>
                                            <small style="font-size:10px;color:#999;">{{ $namaBln[$m] }}</small>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar SPPD terakhir -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-history"></i> SPPD Terakhir Dibuat</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Pegawai</th>
                                            <th>Kegiatan</th>
                                            <th>Tgl</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($spd_terakhir as $row)
                                            <tr>
                                                <td class="text-primary">{{ $row->nomor_spd ?? '-' }}</td>
                                                <td>{{ optional($row->pegawai)->nama_pegawai ?? '-' }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($row->kegiatan_spd, 35) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                                                <td>{!! str_status_sppd($row->status_spd) !!}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">Belum ada data SPPD
                                                    tahun ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
