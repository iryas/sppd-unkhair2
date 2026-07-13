@extends('layouts.backend')

@section('content')
    @php
        $pengaturan = pengaturan();
    @endphp
    <div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard <small class="text-muted">Kepegawaian</small></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6 text-sm-right">
                        <a href="{{ route('kepegawaian.sppd.index') }}" class="btn btn-sm btn-primary"><i
                                class="fas fa-file-alt"></i> Laporan SPPD</a>
                        <a href="{{ route('kepegawaian.std.index') }}" class="btn btn-sm btn-success"><i
                                class="fas fa-file-alt"></i> Laporan STD</a>
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
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $jml_sppd }}</h3>
                                <p>SPPD {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-plane-departure"></i></div>
                            <a href="{{ route('kepegawaian.sppd.index') }}" class="small-box-footer">Lihat laporan <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $jml_stugas }}</h3>
                                <p>Surat Tugas {{ $tahun }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-signature"></i></div>
                            <a href="{{ route('kepegawaian.std.index') }}" class="small-box-footer">Lihat laporan <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $jml_pegawai }}</h3>
                                <p>Pegawai</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <span class="small-box-footer">&nbsp;</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $jml_departemen }}</h3>
                                <p>Departemen/Unit</p>
                            </div>
                            <div class="icon"><i class="fas fa-building"></i></div>
                            <span class="small-box-footer">&nbsp;</span>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

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
