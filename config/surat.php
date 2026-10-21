<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Format Penomoran Surat (SPPD & STD)
    |--------------------------------------------------------------------------
    |
    | Format final nomor surat:
    |   {nomor}/{prefix}/{instansi}/{kode_surat}/{tahun}
    |   contoh: 1549/DST/UN44/HM.04/2026
    |
    | Ubah format cukup di sini (lewat helper nomor_surat_build / _parse).
    | Kosongkan 'prefix' kalau mau tanpa "DST": {nomor}/{instansi}/{kode}/{tahun}
    |
    */

    // Kode instansi (UN44 = Universitas Khairun)
    'instansi' => 'UN44',

    // Prefix setelah nomor urut (kosongkan '' untuk tanpa prefix)
    'prefix' => 'DST',

];
