@extends('layouts.backend')

@section('content')
    @php
        $namaBln = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $maxBln = max(1, max($rst_bulanan));
    @endphp

    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard <small class="text-muted">Review ST</small></h1>
                    </div>
                    <div class="col-sm-6 text-sm-right">
                        <a href="{{ route('admin.std.review') }}" class="btn btn-primary">
                            <i class="fas fa-clipboard-check"></i> Verifikasi STD
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content pl-2 pr-2">
            <div class="container-fluid">

                <div class="callout callout-info py-2">
                    <small>Menampilkan surat tugas untuk pimpinan: <b>{{ $rst_nama }}</b></small>
                </div>

                <!-- Kartu ringkasan -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $rst_menunggu }}</h3>
                                <p>Menunggu Verifikasi</p>
                            </div>
                            <div class="icon"><i class="fas fa-inbox"></i></div>
                            <a href="{{ route('admin.std.review') }}" class="small-box-footer">Verifikasi sekarang <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $rst_verified }}</h3>
                                <p>Terverifikasi {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                            <a href="{{ route('admin.std.review') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3 style="font-size:1.7rem;"><i class="fas fa-clipboard-check"></i></h3>
                                <p>Halaman Verifikasi Surat Tugas</p>
                            </div>
                            <div class="icon"><i class="fas fa-tasks"></i></div>
                            <a href="{{ route('admin.std.review') }}" class="small-box-footer">Buka Verifikasi <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Grafik verifikasi per bulan -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-bar"></i> STD Terverifikasi per Bulan
                                    {{ $tahun }}</h3>
                            </div>
                            <div class="card-body">
                                <div style="display:flex;align-items:flex-end;gap:5px;height:170px;">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;">
                                            <small style="font-size:10px;color:#666;">{{ $rst_bulanan[$m] }}</small>
                                            <div title="{{ $namaBln[$m] }}: {{ $rst_bulanan[$m] }}"
                                                style="width:100%;background:#012970;border-radius:3px 3px 0 0;height:{{ round(($rst_bulanan[$m] / $maxBln) * 130) }}px;min-height:2px;">
                                            </div>
                                            <small style="font-size:10px;color:#999;">{{ $namaBln[$m] }}</small>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Antrian verifikasi -->
                    <div class="col-lg-7">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-inbox"></i> STD Menunggu Verifikasi</h3>
                            </div>
                            <div class="card-body p-0">
                                @if ($rst_antrian->count())
                                    <table class="table table-sm table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nomor</th>
                                                <th>Kegiatan</th>
                                                <th>Jenis</th>
                                                <th>Tgl</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rst_antrian as $row)
                                                <tr>
                                                    <td class="text-primary">{{ $row->nomor_std ?? '-' }}</td>
                                                    <td>{{ \Illuminate\Support\Str::limit($row->kegiatan_std, 28) }}</td>
                                                    <td>
                                                        @if ($row->std_dk)
                                                            <span class="badge badge-secondary">Dalam Kota</span>
                                                        @else
                                                            <span class="badge badge-primary">Luar Kota</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                                                    <td class="text-right">
                                                        <a href="{{ route('admin.std.review') }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-search"></i> Verifikasi
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada antrian &mdash; semua STD sudah diverifikasi.</p>
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
