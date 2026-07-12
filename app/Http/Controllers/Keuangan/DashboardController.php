<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Pegawai;
use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $tahun = tahun();

        // fokus pencairan SPPD yang sudah disetujui (status 200)
        $base = fn () => SuratPerjalananDinas::tahun($tahun)->where('status_spd', 200);
        $belumScope = fn ($q) => $q->where(function ($w) {
            $w->whereNull('nilai_pencairan')->orWhere('nilai_pencairan', 0);
        });

        $kw_belum = $base()->where($belumScope)->count();
        $kw_sudah = $base()->where('nilai_pencairan', '>', 0)->count();
        $kw_total = (float) $base()->sum('nilai_pencairan');

        $bulanan = $base()->where('nilai_pencairan', '>', 0)
            ->selectRaw('MONTH(created_at) as bln, SUM(nilai_pencairan) as total')
            ->groupBy('bln')->pluck('total', 'bln')->toArray();
        $kw_bulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $kw_bulanan[$m] = (float) ($bulanan[$m] ?? 0);
        }

        $kw_antrian = $base()->with('pegawai')->where($belumScope)
            ->orderBy('created_at', 'asc')->limit(8)->get();

        $data = compact('tahun', 'kw_belum', 'kw_sudah', 'kw_total', 'kw_bulanan', 'kw_antrian');

        return view('backend.keuangan.dashboard', $data);
    }
}
