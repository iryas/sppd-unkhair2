<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\SuratPerjalananDinas;
use App\Models\SuratTugasDinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebController extends Controller
{
    public function index()
    {
        return view('layouts.frontent2');
    }

    public function lihatdokumen($params)
    {
        if (!$params) {
            abort(404);
            exit();
        }

        $params = decode_arr($params);
        if (!$params) {
            abort(403);
            exit();
        }

        // dd($params);

        $berkas = Berkas::where('id', $params['berkas_id'])->first();
        if ($berkas && in_array(strtolower($berkas->type_berkas), ['jpg', 'jpeg'])) {
            echo "
            <!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <link id='favicon' rel='shortcut icon' type='image/x-icon' href='" . asset('images/') . pengaturan('logo') . "' />
                    <title>" . pengaturan('nama-sub-aplikasi') . "</title>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css' integrity='sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M' crossorigin='anonymous'>

                    <style>
                    .avatar {
                        /*    background-color: #7d7d7d;*/
                        border: 2px solid #7d7d7d;
                        padding: 2px;
                    }
                    </style>
                </head>
                <body>
                <div class='container'>
                    <center>
                        <p>
                            <img src='" . $berkas->url_berkas . "' alt='blgo image' class='avatar mt-5'>
                        </p>
                    </center>
                </div>
                </body>
            </html>
            ";
            exit();
        } elseif ($berkas && strtolower($berkas->type_berkas) == 'pdf') {
            echo "
            <!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <link id='favicon' rel='shortcut icon' type='image/x-icon' href='" . asset('images/') . pengaturan('logo') . "' />
                    <title>" . pengaturan('nama-sub-aplikasi') . "</title>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css' integrity='sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M' crossorigin='anonymous'>

                    <style>
                    .avatar {
                        /*    background-color: #7d7d7d;*/
                        border: 2px solid #7d7d7d;
                        padding: 2px;
                    }
                    </style>
                </head>
                <body>
                    <center>
                        <iframe src='" . $berkas->url_berkas . "' width='850' height='600'></iframe>
                    </center>
                </body>
            </html>
            ";
            exit();
        } else {
            abort(404);
            exit();
        }
    }

    public function verifikasi_spd($params)
    {
        if (!$params) {
            abort(404);
            exit();
        }

        $params = decode_arr($params);
        if (!$params) {
            abort(403);
            exit();
        }

        $get = SuratPerjalananDinas::with(['pegawai', 'departemen', 'reviwer', 'user'])->where('id', $params['sppd_id'])->first();
        // dd($get);
        $data = [
            'judul' => 'Data Pengajuan SPPD',
            'get' => $get
        ];
        return view('detail-sppd', $data);
    }

    public function verifikasi_std($params)
    {
        if (!$params) {
            abort(404);
            exit();
        }

        $params = decode_arr($params);
        if (!$params) {
            abort(403);
            exit();
        }

        $get = SuratTugasDinas::with(['pegawai', 'departemen', 'reviwer', 'user'])->where('id', $params['stugas_id'])->first();
        // dd($get);
        $data = [
            'judul' => 'Data Pengajuan STD',
            'get' => $get
        ];
        return view('detail-std', $data);
    }

    /**
     * Verifikasi keaslian surat dari halaman depan berdasarkan nomor surat.
     * Mencari di SPPD (nomor_spd) lalu STD (nomor_std) yang sudah terverifikasi.
     */
    public function verifikasi_cari(Request $request)
    {
        $nomor = trim((string) $request->query('nomor'));

        if ($nomor === '') {
            return redirect()->route('frontend.site')
                ->with('verif_error', 'Silakan masukkan nomor surat terlebih dahulu.')
                ->withFragment('verifikasi');
        }

        $spd = SuratPerjalananDinas::with(['pegawai', 'departemen', 'reviwer', 'user'])
            ->where('nomor_spd', $nomor)->where('status_spd', 200)->first();
        if ($spd) {
            return view('detail-sppd', ['judul' => 'Data Pengajuan SPPD', 'get' => $spd]);
        }

        $std = SuratTugasDinas::with(['pegawai', 'departemen', 'reviwer', 'user'])
            ->where('nomor_std', $nomor)->where('status_std', 200)->first();
        if ($std) {
            return view('detail-std', ['judul' => 'Data Pengajuan STD', 'get' => $std]);
        }

        return redirect()->route('frontend.site')
            ->with('verif_error', 'Surat dengan nomor "' . $nomor . '" tidak ditemukan atau belum terverifikasi.')
            ->withFragment('verifikasi');
    }
}
