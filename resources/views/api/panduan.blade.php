<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan API — SPPD UNKHAIR</title>
    <style>
        :root{
            --ink:#1f2328; --muted:#656d76; --line:#e6e8eb;
            --accent:#1b7a3a; --code-bg:#f6f8fa;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
            color:var(--ink);background:#fff;line-height:1.65;font-size:15px}
        .wrap{max-width:720px;margin:0 auto;padding:40px 20px 72px}
        h1{font-size:22px;font-weight:600}
        .sub{color:var(--muted);margin-top:6px}
        .ver{color:var(--muted);font-size:13px;margin-top:8px}
        .hr{height:1px;background:var(--line);border:0;margin:24px 0}
        h2{font-size:16px;font-weight:600;margin:32px 0 12px}
        p{margin:8px 0}
        code{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:13.5px;
            background:var(--code-bg);padding:1px 6px;border-radius:4px;color:#24413a}
        .ep{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:15px;font-weight:600;margin-top:4px}
        .ep .m{color:var(--accent);margin-right:6px}
        .tag{font-size:12px;color:var(--muted);font-weight:400;margin-left:8px}
        table{width:100%;border-collapse:collapse;margin:12px 0}
        th,td{text-align:left;padding:8px 4px;font-size:14px;border-bottom:1px solid var(--line);vertical-align:top}
        th{color:var(--muted);font-weight:500;font-size:13px;width:120px}
        td:first-child,th:first-child{white-space:nowrap}
        pre{background:var(--code-bg);padding:14px 16px;border-radius:8px;overflow-x:auto;
            font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px;line-height:1.6;
            color:var(--ink);margin:10px 0}
        pre .c{color:var(--muted)}
        ul{margin:8px 0 8px 20px}
        li{margin:5px 0;font-size:14px}
        .note{color:var(--muted);font-size:13.5px;margin-top:8px}
        footer{color:var(--muted);font-size:13px;margin-top:44px;padding-top:16px;border-top:1px solid var(--line)}
    </style>
</head>
<body>
<div class="wrap">

    <h1>Panduan API SPPD UNKHAIR</h1>
    <p class="sub">Data pegawai yang sedang melaksanakan tugas dinas (luar &amp; dalam kota),
       untuk integrasi absensi SIMPEG.</p>
    <p class="ver">Versi 1.0</p>

    <hr class="hr">

    <h2>Autentikasi</h2>
    <p>Setiap request ke endpoint data wajib menyertakan header:</p>
    <pre>X-API-KEY: {api_key_anda}</pre>
    <p class="note">Tanpa header atau key salah akan mendapat <code>HTTP 401</code>.
       Minta API key ke pengelola aplikasi (UPT TIK).</p>

    <h2>1. Daftar pegawai sedang tugas dinas</h2>
    <p class="ep"><span class="m">GET</span>/api/dinas <span class="tag">butuh API key</span></p>
    <p class="note">Untuk rekap absensi — pegawai yang sedang bertugas pada tanggal / rentang tertentu.</p>
    <table>
        <tr><th>Parameter</th><th>Keterangan</th></tr>
        <tr><td><code>tanggal</code></td><td>YYYY-MM-DD — cek harian (pilih ini atau mulai+selesai)</td></tr>
        <tr><td><code>mulai</code></td><td>Awal rentang (berpasangan dengan selesai)</td></tr>
        <tr><td><code>selesai</code></td><td>Akhir rentang</td></tr>
        <tr><td><code>nip</code></td><td>Opsional — filter satu pegawai</td></tr>
        <tr><td><code>jenis</code></td><td>Opsional — <code>luar</code> atau <code>dalam</code></td></tr>
    </table>
    <pre><span class="c"># Contoh</span>
GET /api/dinas?tanggal=2025-08-24
GET /api/dinas?mulai=2025-08-01&selesai=2025-08-31
GET /api/dinas?tanggal=2025-08-24&jenis=luar</pre>

    <h2>2. Pencarian surat tugas</h2>
    <p class="ep"><span class="m">GET</span>/api/dinas/cari <span class="tag">butuh API key</span></p>
    <table>
        <tr><th>Parameter</th><th>Keterangan</th></tr>
        <tr><td><code>nip</code></td><td>Cari semua surat tugas milik NIP tersebut</td></tr>
        <tr><td><code>nomor</code></td><td>Cari berdasarkan nomor_std atau nomor_spd</td></tr>
        <tr><td><code>tahun</code></td><td>Opsional — YYYY</td></tr>
    </table>
    <p class="note">Wajib mengirim minimal salah satu: <code>nip</code> atau <code>nomor</code>.</p>
    <pre><span class="c"># Contoh</span>
GET /api/dinas/cari?nip=199001011999031001&tahun=2025
GET /api/dinas/cari?nomor=0123/UN44/OT.00/2025</pre>

    <h2>Format response</h2>
    <pre>{
  "status": true,
  "jumlah": 112,
  "data": [
    {
      "nip": "199001011999031001",
      "nama": "Budi Santoso, S.T., M.T.",
      "nomor_std": "0123/UN44/RT.00/2025",
      "nomor_spd": "0123/UN44/RT.00/2025",
      "kegiatan": "...",
      "tujuan": "Jakarta",
      "jenis": "luar_kota",
      "tanggal_mulai": "2025-08-24",
      "tanggal_selesai": "2025-08-26",
      "status": "terverifikasi"
    }
  ]
}</pre>
    <p class="note"><code>tujuan</code> dan <code>nomor_spd</code> bernilai <code>null</code> untuk dinas dalam kota.
       <code>jenis</code> berisi <code>luar_kota</code> atau <code>dalam_kota</code>.</p>

    <h2>Contoh akses (PHP)</h2>
    <p class="note">Menggunakan cURL bawaan PHP, tanpa library tambahan.</p>
    <pre>&lt;?php
$base    = 'https://sppd.unkhair.ac.id';   <span class="c">// URL aplikasi SPPD</span>
$apiKey  = 'API_KEY_ANDA';                 <span class="c">// minta ke UPT TIK</span>
$tanggal = date('Y-m-d');                  <span class="c">// tanggal absensi</span>

$ch = curl_init($base . '/api/dinas?tanggal=' . $tanggal);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['X-API-KEY: ' . $apiKey],
]);
$response = curl_exec($ch);
$http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http === 200) {
    $hasil = json_decode($response, true);
    foreach ($hasil['data'] as $row) {
        <span class="c">// di SIMPEG: tandai NIP ini sebagai "Dinas", bukan Alpha</span>
        echo $row['nip'] . ' - ' . $row['nama'] . ' (' . $row['jenis'] . ")\n";
    }
} else {
    echo "Gagal akses API (HTTP $http): " . $response;
}</pre>

    <p class="note">Untuk Laravel bisa memakai <code>Http::withHeaders(['X-API-KEY' =&gt; $apiKey])-&gt;get(...)</code>.</p>

    <h2>Kode status</h2>
    <table>
        <tr><td><code>200</code></td><td>Permintaan berhasil</td></tr>
        <tr><td><code>401</code></td><td>API key tidak valid / tidak dikirim</td></tr>
        <tr><td><code>422</code></td><td>Parameter tidak valid atau kurang</td></tr>
    </table>

    <h2>Catatan</h2>
    <ul>
        <li>Pencocokan pegawai di sisi SIMPEG menggunakan NIP.</li>
        <li>Hanya surat berstatus terverifikasi (disetujui) yang ditampilkan.</li>
        <li>Data mencakup dinas luar kota dan dalam kota sekaligus.</li>
        <li>Halaman ini publik — <code>GET /api/dinas/panduan</code> (tanpa API key).</li>
    </ul>

    <footer>
        &copy; {{ date('Y') }} UPT TIK — Universitas Khairun Ternate · SPPD UNKHAIR API v1.0
    </footer>

</div>
</body>
</html>
