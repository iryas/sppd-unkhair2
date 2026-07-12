@extends('layouts.backend')

@section('content')
    @php
        $namaBln = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $maxBln = max(1, max($kw_bulanan));
        $rpSingkat = function ($n) {
            if ($n >= 1000000000) {
                return number_format($n / 1000000000, 1, ',', '.') . ' M';
            }
            if ($n >= 1000000) {
                return number_format($n / 1000000, 0, ',', '.') . ' Jt';
            }
            if ($n > 0) {
                return number_format($n / 1000, 0, ',', '.') . ' rb';
            }
            return '0';
        };
    @endphp

    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard <small class="text-muted">Keuangan</small></h1>
                    </div>
                    <div class="col-sm-6 text-sm-right">
                        <a href="{{ route('keuangan.sppd.index') }}" class="btn btn-primary">
                            <i class="fas fa-money-bill-wave"></i> Proses Pencairan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content pl-2 pr-2">
            <div class="container-fluid">

                <!-- Kartu ringkasan pencairan -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $kw_belum }}</h3>
                                <p>Belum Dicairkan</p>
                            </div>
                            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                            <a href="{{ route('keuangan.sppd.index') }}" class="small-box-footer">Proses sekarang <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $kw_sudah }}</h3>
                                <p>Sudah Dicairkan</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                            <a href="{{ route('keuangan.sppd.index') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3 style="font-size:1.9rem;">Rp {{ rupiah($kw_total) }}</h3>
                                <p>Total Nilai Pencairan SPPD {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-coins"></i></div>
                            <span class="small-box-footer">&nbsp;</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Grafik pencairan per bulan -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Nilai Pencairan per Bulan
                                    {{ $tahun }}</h3>
                            </div>
                            <div class="card-body">
                                <div style="display:flex;align-items:flex-end;gap:5px;height:170px;">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;">
                                            <small style="font-size:9px;color:#666;">{{ $rpSingkat($kw_bulanan[$m]) }}</small>
                                            <div title="{{ $namaBln[$m] }}: Rp {{ rupiah($kw_bulanan[$m]) }}"
                                                style="width:100%;background:#012970;border-radius:3px 3px 0 0;height:{{ round(($kw_bulanan[$m] / $maxBln) * 125) }}px;min-height:2px;">
                                            </div>
                                            <small style="font-size:10px;color:#999;">{{ $namaBln[$m] }}</small>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Antrian belum dicairkan -->
                    <div class="col-lg-7">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-inbox"></i> SPPD Belum Dicairkan</h3>
                            </div>
                            <div class="card-body p-0">
                                @if ($kw_antrian->count())
                                    <table class="table table-sm table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nomor</th>
                                                <th>Pegawai</th>
                                                <th>Tujuan</th>
                                                <th>Tgl</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($kw_antrian as $row)
                                                <tr>
                                                    <td class="text-primary">{{ $row->nomor_spd }}</td>
                                                    <td>{{ optional($row->pegawai)->nama_pegawai ?? '-' }}</td>
                                                    <td>{{ $row->tujuan ?? '-' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                                                    <td class="text-right">
                                                        <a href="{{ route('keuangan.sppd.index') }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-money-bill"></i> Input
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <p class="mb-0">Semua SPPD tahun ini sudah dicairkan.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
