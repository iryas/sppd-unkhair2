<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $tahun = tahun();

        $jml_pegawai = Pegawai::all()->count();
        $jml_departemen = Departemen::departemen(NULL)->get()->count();
        $jml_sppd = SuratPerjalananDinas::tahun($tahun)->status_spd(['200'])->get()->count();
        $jml_stugas = SuratTugasDinas::tahun($tahun)->status_std(['200'])->get()->count();

        $data = [
            'jml_pegawai' => $jml_pegawai,
            'jml_departemen' => $jml_departemen,
            'jml_sppd' => $jml_sppd,
            'jml_stugas' => $jml_stugas,
            'tahun' => $tahun
        ];

        if (auth()->user()->hasRole('ppk') && in_array(session('role'), ['ppk'])) {
            $datatable = [
                'tahun' => tahun(),
                'datatable_departemen' => [
                    'url' => route('admin.dashboard.satistik-departemen'),
                    'id_table' => 'id-datatable1',
                    'columns' => [
                        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                        ['data' => 'departemen', 'name' => 'departemen', 'orderable' => 'false', 'searchable' => 'true'],
                        ['data' => 'jml_sppd', 'name' => 'jml_sppd', 'orderable' => 'false', 'searchable' => 'false'],
                        ['data' => 'jml_std', 'name' => 'jml_std', 'orderable' => 'false', 'searchable' => 'false']
                    ]
                ],
                'datatable_pegawai' => [
                    'url' => route('admin.dashboard.satistik-pegawai'),
                    'id_table' => 'id-datatable2',
                    'columns' => [
                        ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => 'false', 'searchable' => 'false'],
                        ['data' => 'nama_pegawai', 'name' => 'nama_pegawai', 'orderable' => 'false', 'searchable' => 'true'],
                        ['data' => 'jabatan', 'name' => 'jabatan', 'orderable' => 'false', 'searchable' => 'false'],
                        ['data' => 'jml_sppd', 'name' => 'jml_sppd', 'orderable' => 'false', 'searchable' => 'false'],
                        ['data' => 'jml_std', 'name' => 'jml_std', 'orderable' => 'false', 'searchable' => 'false']
                    ]
                ]
            ];

            $data = array_merge($data, $datatable, $this->dataDashboardPpk($tahun));
            return view('backend.admin.dashboard-ppk', $data);
        } elseif (auth()->user()->hasRole('admin-spd') && session('role') === 'admin-spd') {
            return view('backend.admin.dashboard-spd', $this->dataDashboardSpd($tahun));
        } elseif (auth()->user()->hasRole('admin-st') && in_array(session('role'), ['admin-st', 'admin-st-dk'])) {
            return view('backend.admin.dashboard-std', $this->dataDashboardStd($tahun));
        } else {
            return view('backend.admin.dashboard', $data);
        }
    }

    /**
     * Data khusus dashboard role Admin ST: ringkasan status STD tahun berjalan,
     * tren bulanan, dan daftar STD terakhir. Fokus actionable: "Belum Lengkap"
     * (STD hasil SPPD yang perlu dilengkapi) dan "Belum Diverifikasi".
     */
    private function dataDashboardStd($tahun)
    {
        $std_terbit       = SuratTugasDinas::tahun($tahun)->whereIn('status_std', [102, 200, 206, 406, 409])->count();
        $std_verified     = SuratTugasDinas::tahun($tahun)->where('status_std', 200)->count();
        $std_belumlengkap = SuratTugasDinas::tahun($tahun)->where('status_std', 206)->count();
        $std_belumverif   = SuratTugasDinas::tahun($tahun)->where('status_std', 102)->count();

        $bulanan = SuratTugasDinas::tahun($tahun)->where('status_std', 200)
            ->selectRaw('MONTH(created_at) as bln, COUNT(*) as jml')
            ->groupBy('bln')->pluck('jml', 'bln')->toArray();
        $std_bulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $std_bulanan[$m] = (int) ($bulanan[$m] ?? 0);
        }

        $std_terakhir = SuratTugasDinas::with('pegawai')->tahun($tahun)
            ->whereIn('status_std', [102, 200, 206, 406, 409])
            ->orderBy('created_at', 'desc')->limit(8)->get();

        return compact('tahun', 'std_terbit', 'std_verified', 'std_belumlengkap', 'std_belumverif', 'std_bulanan', 'std_terakhir');
    }

    /**
     * Data khusus dashboard role PPK: ringkasan status SPPD tahun berjalan +
     * antrian SPPD yang menunggu persetujuan (status 102).
     */
    private function dataDashboardPpk($tahun)
    {
        $ppk_menunggu   = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 102)->count();
        $ppk_disetujui  = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 200)->count();
        $ppk_dibatalkan = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 409)->count();

        $ppk_antrian = SuratPerjalananDinas::with('pegawai')->tahun($tahun)
            ->where('status_spd', 102)
            ->orderBy('created_at', 'asc')->limit(8)->get();

        return compact('ppk_menunggu', 'ppk_disetujui', 'ppk_dibatalkan', 'ppk_antrian');
    }

    /**
     * Data khusus dashboard role Admin SPD: ringkasan status SPPD tahun berjalan,
     * tren bulanan, dan daftar SPPD terakhir dibuat.
     */
    private function dataDashboardSpd($tahun)
    {
        $spd_diajukan   = SuratPerjalananDinas::tahun($tahun)->whereIn('status_spd', [102, 200, 406, 409])->count();
        $spd_disetujui  = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 200)->count();
        $spd_menunggu   = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 102)->count();
        $spd_dibatalkan = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 409)->count();

        $bulanan = SuratPerjalananDinas::tahun($tahun)->where('status_spd', 200)
            ->selectRaw('MONTH(created_at) as bln, COUNT(*) as jml')
            ->groupBy('bln')->pluck('jml', 'bln')->toArray();
        $spd_bulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $spd_bulanan[$m] = (int) ($bulanan[$m] ?? 0);
        }

        $spd_terakhir = SuratPerjalananDinas::with('pegawai')->tahun($tahun)
            ->whereIn('status_spd', [102, 200, 406, 409])
            ->orderBy('created_at', 'desc')->limit(8)->get();

        return compact('tahun', 'spd_diajukan', 'spd_disetujui', 'spd_menunggu', 'spd_dibatalkan', 'spd_bulanan', 'spd_terakhir');
    }

    public function get_statistik_usulan_departemen(Request $request)
    {
        if ($request->ajax()) {
            $listdata = Departemen::withCount([
                'sppd AS jml_sppd' => function (Builder $query) {
                    $query->whereYear('created_at', tahun())
                        ->where('status_spd', '200');
                },
                'std AS jml_std' => function (Builder $query) {
                    $query->whereYear('created_at', tahun())
                        ->where('status_std', '200');
                }
            ])->where('parent_id', NULL)->orderBy('created_at', 'ASC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('jml_sppd', function ($row) {
                    return $row->jml_sppd ?? 0;
                })
                ->editColumn('jml_std', function ($row) {
                    return $row->jml_std ?? 0;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('departemen', 'LIKE', "%$search%")->orWhere('lokasi', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['jml_sppd', 'jml_std'])
                ->make(true);
        }
    }

    public function get_statistik_usulan_pegawai(Request $request)
    {
        if ($request->ajax()) {
            $listdata = Pegawai::withCount([
                'sppd as jml_sppd' => function (Builder $query) {
                    $query->whereYear('created_at', tahun())
                        ->where('status_spd', '200');
                },
                'std AS jml_std' => function (Builder $query) {
                    $query->whereYear('created_at', tahun())
                        ->where('status_std', '200');
                }
            ])->orderBy('nama_pegawai', 'ASC');
            return DataTables::eloquent($listdata)
                ->addIndexColumn()
                ->editColumn('nama_pegawai', function ($row) {
                    $str = '
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item p-0">' . $row->nama_pegawai . '</li>
                        <li class="list-group-item p-0">NIP: ' . ($row->nip ?? '-') . '</li>
                    </ul>
                    ';
                    return $str;
                })
                ->editColumn('jml_sppd', function ($row) {
                    return $row->jml_sppd ?? 0;
                })
                ->editColumn('jml_std', function ($row) {
                    return $row->jml_std ?? 0;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->input('search.value'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->input('search.value');
                            $w->orWhere('nik', 'LIKE', "%$search%")
                                ->orWhere('nip', 'LIKE', "%$search%")
                                ->orWhere('nama_pegawai', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['nama_pegawai', 'jml_sppd', 'jml_std'])
                ->make(true);
        }
    }
}
