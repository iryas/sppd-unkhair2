@extends('layouts.backend')

@section('content')
    @php
        $pengaturan = pengaturan();
    @endphp
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content pl-2 pr-2">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $ppk_menunggu }}</h3>
                                <p>Menunggu Persetujuan</p>
                            </div>
                            <div class="icon"><i class="fas fa-inbox"></i></div>
                            <a href="{{ route('admin.sppd.review') }}" class="small-box-footer">Review sekarang <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $ppk_disetujui }}</h3>
                                <p>Disetujui {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                            <a href="{{ route('admin.sppd.index') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $ppk_dibatalkan }}</h3>
                                <p>Dibatalkan {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-ban"></i></div>
                            <a href="{{ route('admin.sppd.pembatalan') }}" class="small-box-footer">Lihat <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3 style="font-size:1.7rem;"><i class="fas fa-clipboard-check"></i></h3>
                                <p>Halaman Review SPPD</p>
                            </div>
                            <div class="icon"><i class="fas fa-tasks"></i></div>
                            <a href="{{ route('admin.sppd.review') }}" class="small-box-footer">Buka Review <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Antrian Review PPK -->
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-inbox"></i> SPPD Menunggu Persetujuan</h3>
                    </div>
                    <div class="card-body p-0">
                        @if ($ppk_antrian->count())
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Pengusul</th>
                                        <th>Kegiatan</th>
                                        <th>Tujuan</th>
                                        <th>Tgl</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ppk_antrian as $row)
                                        <tr>
                                            <td class="text-primary">{{ $row->nomor_spd }}</td>
                                            <td>{{ optional($row->pegawai)->nama_pegawai ?? '-' }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($row->kegiatan_spd, 30) }}</td>
                                            <td>{{ $row->tujuan ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.sppd.review') }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-search"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada antrian &mdash; semua usulan SPPD sudah direview.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center g-5">
                            <div class="col-lg-3">
                                <img src="{{ asset('images/dashboard.png') }}" class="img-fluid opacity-85" alt="images"
                                    loading="lazy">
                            </div>
                            <div class="col-lg-9 px-xl-5">
                                <h4 class="mb-2">
                                    Selamat datang <b>{{ Auth::user()->name }}</b> di {{ $pengaturan['nama-sub-aplikasi'] }}
                                    {{ $pengaturan['nama-departemen'] }}
                                </h4>
                                <p class="lead-dashboard mb-4">
                                    {{ $pengaturan['nama-sub-aplikasi'] }}
                                    merupakan sistem informasi yang dirancang khusus untuk mengelola data
                                    <span title="Surat Perintah Perjalanan Dinas">SPPD</span>
                                    dan <span title="Surat Tugas Dinas">STD</span>.
                                    Sehingga Universitas Khairun dapat menyediakan layanan yang lebih efektif dan efisien.
                                </p>
                                <div class="d-grid gap-3 d-md-flex justify-content-md-start">
                                    <livewire:auth.logout tampilan="logout2" />
                                    <livewire:auth.profile />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- statistik pengajuan sppd / std -->
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Usulan Departemen/Unit {{ $tahun }}</h3>

                                <div class="card-tools"></div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive p-0 mb-2">
                                    <table class="table table-condensed table-bordered"
                                        id="{{ $datatable_departemen['id_table'] }}">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Departemen</th>
                                                <th>SPPD</th>
                                                <th>STD</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Usulan Pegawai {{ $tahun }}</h3>

                                <div class="card-tools"></div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive p-0 mb-2">
                                    <table class="table table-condensed table-bordered"
                                        id="{{ $datatable_pegawai['id_table'] }}">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Pegawai</th>
                                                <th>Jabatan</th>
                                                <th>SPPD</th>
                                                <th>STD</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    @push('style')
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    @endpush

    @push('script')
        <script type="text/javascript">
            var table_departemen;
            var table_pegawai;
            $(function() {
                table_departemen = $("#{{ $datatable_departemen['id_table'] }}").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable_departemen['url'] }}",
                    },
                    columns: [
                        @foreach ($datatable_departemen['columns'] as $row)
                            {
                                data: "{{ $row['data'] }}",
                                name: "{{ $row['name'] }}",
                                orderable: {{ $row['orderable'] }},
                                searchable: {{ $row['searchable'] }}
                            },
                        @endforeach
                    ]
                });

                table_pegawai = $("#{{ $datatable_pegawai['id_table'] }}").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ $datatable_pegawai['url'] }}",
                    },
                    columns: [
                        @foreach ($datatable_pegawai['columns'] as $row)
                            {
                                data: "{{ $row['data'] }}",
                                name: "{{ $row['name'] }}",
                                orderable: {{ $row['orderable'] }},
                                searchable: {{ $row['searchable'] }}
                            },
                        @endforeach
                    ]
                });
            });
        </script>
    @endpush
@endsection
