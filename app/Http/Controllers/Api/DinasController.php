<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * API untuk integrasi SIMPEG (modul absensi).
 *
 * Sumber data: Surat Tugas Dinas (app_surat_tugas_dinas) yang terverifikasi
 * (status_std = 200), mencakup DUA jenis sekaligus:
 *   - Luar kota (std_dk = 0, berasal dari SPD)
 *   - Dalam kota (std_dk = 1)
 * Daftar pegawai diambil dari pivot app_surat_tugas_dinas_has_pegawai.
 * Pencocokan pegawai di sisi SIMPEG memakai NIP.
 */
class DinasController extends Controller
{
    /**
     * Daftar pegawai yang sedang tugas dinas pada tanggal / rentang tertentu.
     * GET /api/dinas?tanggal=Y-m-d  ATAU  ?mulai=Y-m-d&selesai=Y-m-d
     * Opsional: &nip=... &jenis=luar|dalam
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'mulai'   => ['nullable', 'date_format:Y-m-d', 'required_with:selesai'],
            'selesai' => ['nullable', 'date_format:Y-m-d', 'required_with:mulai', 'after_or_equal:mulai'],
            'nip'     => ['nullable', 'string', 'max:30'],
            'jenis'   => ['nullable', 'in:luar,dalam'],
        ]);

        if ($validator->fails()) {
            return $this->invalid($validator->errors());
        }

        $tanggal = $request->query('tanggal');
        $mulai   = $request->query('mulai');
        $selesai = $request->query('selesai');

        if ($tanggal) {
            $mulai = $selesai = $tanggal;
        } elseif (!$mulai || !$selesai) {
            return response()->json([
                'status'  => false,
                'message' => 'Wajib mengirim parameter "tanggal" atau pasangan "mulai" & "selesai".',
            ], 422);
        }

        $query = $this->baseQuery()
            ->whereDate('std.tanggal_mulai_tugas', '<=', $selesai)
            ->whereDate('std.tanggal_selesai_tugas', '>=', $mulai)
            ->when($request->query('nip'), fn ($q) => $q->where('p.nip', $request->query('nip')))
            ->when($request->query('jenis') === 'luar', fn ($q) => $q->where('std.std_dk', 0))
            ->when($request->query('jenis') === 'dalam', fn ($q) => $q->where('std.std_dk', 1));

        return $this->respond($query, ['mulai' => $mulai, 'selesai' => $selesai]);
    }

    /**
     * Pencarian surat tugas berdasarkan NIP dan/atau nomor surat.
     * GET /api/dinas/cari?nip=...            -> semua tugas pegawai tsb
     * GET /api/dinas/cari?nomor=...          -> cocok ke nomor_std ATAU nomor_spd
     * Opsional: &tahun=YYYY
     */
    public function cari(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip'   => ['nullable', 'string', 'max:30'],
            'nomor' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'digits:4'],
        ]);

        if ($validator->fails()) {
            return $this->invalid($validator->errors());
        }

        $nip   = $request->query('nip');
        $nomor = $request->query('nomor');
        $tahun = $request->query('tahun');

        if (!$nip && !$nomor) {
            return response()->json([
                'status'  => false,
                'message' => 'Wajib mengirim minimal salah satu parameter: "nip" atau "nomor".',
            ], 422);
        }

        $query = $this->baseQuery()
            ->when($nip, fn ($q) => $q->where('p.nip', $nip))
            ->when($nomor, function ($q) use ($nomor) {
                $q->where(function ($w) use ($nomor) {
                    $w->where('std.nomor_std', $nomor)
                        ->orWhere('spd.nomor_spd', $nomor);
                });
            })
            ->when($tahun, fn ($q) => $q->whereYear('std.tanggal_mulai_tugas', $tahun));

        return $this->respond($query, ['nip' => $nip, 'nomor' => $nomor, 'tahun' => $tahun]);
    }

    /**
     * Query dasar: join STD + pivot pegawai + pegawai (+ SPD untuk data luar kota).
     * Hanya surat terverifikasi (status_std = 200) dan pegawai ber-NIP.
     */
    private function baseQuery(): Builder
    {
        return DB::table('app_surat_tugas_dinas_has_pegawai as pv')
            ->join('app_surat_tugas_dinas as std', 'std.id', '=', 'pv.surat_tugas_dinas_id')
            ->join('app_pegawai as p', 'p.id', '=', 'pv.pegawai_id')
            ->leftJoin('app_surat_perjalanan_dinas as spd', 'spd.id', '=', 'std.spd_id')
            ->where('std.status_std', 200)
            ->whereNotNull('std.tanggal_mulai_tugas')
            ->whereNotNull('std.tanggal_selesai_tugas')
            ->whereNotNull('p.nip')
            ->where('p.nip', '!=', '')
            ->select([
                'p.nip',
                'p.nama_pegawai as nama',
                'std.nomor_std',
                'spd.nomor_spd',
                'std.kegiatan_std as kegiatan',
                'spd.tujuan',
                'std.std_dk',
                'std.tanggal_mulai_tugas as tanggal_mulai',
                'std.tanggal_selesai_tugas as tanggal_selesai',
            ])
            ->orderBy('p.nama_pegawai')
            ->orderBy('std.tanggal_mulai_tugas');
    }

    private function respond(Builder $query, array $filter)
    {
        $data = $query->get()->map(fn ($row) => [
            'nip'             => $row->nip,
            'nama'            => $row->nama,
            'nomor_std'       => $row->nomor_std,
            'nomor_spd'       => $row->nomor_spd, // null untuk dinas dalam kota
            'kegiatan'        => $row->kegiatan,
            'tujuan'          => $row->tujuan,     // null untuk dinas dalam kota
            'jenis'           => $row->std_dk == 1 ? 'dalam_kota' : 'luar_kota',
            'tanggal_mulai'   => $row->tanggal_mulai,
            'tanggal_selesai' => $row->tanggal_selesai,
            'status'          => 'terverifikasi',
        ]);

        return response()->json([
            'status'  => true,
            'message' => $data->count() ? 'Data ditemukan.' : 'Data tidak ditemukan.',
            'filter'  => array_filter($filter, fn ($v) => $v !== null),
            'jumlah'  => $data->count(),
            'data'    => $data,
        ]);
    }

    private function invalid($errors)
    {
        return response()->json([
            'status'  => false,
            'message' => 'Parameter tidak valid.',
            'errors'  => $errors,
        ], 422);
    }
}
