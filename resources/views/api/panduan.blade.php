<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan API &mdash; SPPD UNKHAIR</title>
    <style>
        :root{
            --primary:#1b5e20; --primary-light:#2e7d32; --accent:#f0f7f0;
            --get:#0d6efd; --border:#e3e8e3; --muted:#6b756b; --code-bg:#1e2a1e;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
            color:#243024;background:#f5f7f5;line-height:1.6;padding:0 16px 60px}
        .wrap{max-width:860px;margin:0 auto}
        header{background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;
            padding:36px 32px;border-radius:0 0 18px 18px;margin:0 -16px 28px;box-shadow:0 4px 16px rgba(0,0,0,.12)}
        header h1{font-size:26px;font-weight:700;letter-spacing:.3px}
        header p{opacity:.92;margin-top:6px;font-size:15px;max-width:640px}
        .badge-ver{display:inline-block;background:rgba(255,255,255,.2);padding:2px 10px;border-radius:20px;
            font-size:12px;margin-top:12px;font-weight:600}
        section{background:#fff;border:1px solid var(--border);border-radius:14px;padding:22px 24px;margin-bottom:18px}
        h2{font-size:17px;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px}
        h2 .ico{font-size:18px}
        .method{display:inline-block;background:var(--get);color:#fff;font-size:12px;font-weight:700;
            padding:3px 9px;border-radius:6px;letter-spacing:.5px;vertical-align:middle}
        .path{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:15px;font-weight:600;
            color:#243024;vertical-align:middle;margin-left:6px}
        .pill{display:inline-block;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:8px}
        .pill.pub{background:#e6f4ea;color:#1b7a3a}
        .pill.key{background:#fff3cd;color:#8a6d1a}
        table{width:100%;border-collapse:collapse;margin:10px 0 4px}
        th,td{text-align:left;padding:8px 10px;font-size:14px;border-bottom:1px solid var(--border);vertical-align:top}
        th{color:var(--muted);font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.4px}
        td code,.inline{font-family:ui-monospace,Menlo,Consolas,monospace;background:var(--accent);
            padding:1px 6px;border-radius:5px;font-size:13px;color:var(--primary-light)}
        pre{background:var(--code-bg);color:#d7ecd7;padding:12px 16px;border-radius:10px;overflow-x:auto;
            font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px;margin:8px 0}
        pre .c{color:#7fd18a}
        .note{background:var(--accent);border-left:4px solid var(--primary-light);padding:12px 16px;
            border-radius:0 8px 8px 0;font-size:14px;margin-top:10px}
        ul.notes{list-style:none;margin-top:4px}
        ul.notes li{padding:6px 0 6px 24px;position:relative;font-size:14px}
        ul.notes li::before{content:"✓";position:absolute;left:0;color:var(--primary-light);font-weight:700}
        .status-row{display:flex;gap:10px;align-items:center;padding:6px 0;font-size:14px}
        .code-num{font-weight:700;font-family:Menlo,monospace;padding:2px 8px;border-radius:6px;min-width:44px;text-align:center}
        .s200{background:#e6f4ea;color:#1b7a3a}.s401{background:#fde7e7;color:#b02a2a}.s422{background:#fff3cd;color:#8a6d1a}
        footer{text-align:center;color:var(--muted);font-size:13px;margin-top:26px}
        .ep-head{margin-bottom:10px}
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h1>📘 Panduan API SPPD UNKHAIR</h1>
        <p>API data pegawai yang sedang melaksanakan tugas dinas (luar kota &amp; dalam kota),
           untuk kebutuhan integrasi absensi SIMPEG.</p>
        <span class="badge-ver">Versi 1.0</span>
    </header>

    <section>
        <h2><span class="ico">🔑</span> Autentikasi</h2>
        <p style="font-size:14px">Setiap request ke endpoint data <b>wajib</b> menyertakan header berikut:</p>
        <pre>X-API-KEY: <span class="c">{api_key_anda}</span></pre>
        <div class="note">Tanpa header, atau API key salah, server akan membalas <b>HTTP 401 Unauthorized</b>.
            Silakan minta API key ke pengelola aplikasi SPPD UNKHAIR (UPT TIK).</div>
    </section>

    <section>
        <div class="ep-head">
            <h2 style="margin-bottom:4px"><span class="ico">📋</span> Daftar Pegawai Sedang Tugas Dinas</h2>
            <span class="method">GET</span><span class="path">/api/dinas</span><span class="pill key">butuh API key</span>
        </div>
        <p style="font-size:14px;color:var(--muted)">Untuk rekap absensi &mdash; menampilkan pegawai yang sedang bertugas pada tanggal / rentang tertentu.</p>
        <table>
            <tr><th>Parameter</th><th>Keterangan</th></tr>
            <tr><td><code>tanggal</code></td><td>Format <span class="inline">YYYY-MM-DD</span> &mdash; cek harian (pilih ini <b>ATAU</b> mulai+selesai)</td></tr>
            <tr><td><code>mulai</code></td><td>Awal rentang (wajib berpasangan dengan <code>selesai</code>)</td></tr>
            <tr><td><code>selesai</code></td><td>Akhir rentang</td></tr>
            <tr><td><code>nip</code></td><td><i>(opsional)</i> filter satu pegawai</td></tr>
            <tr><td><code>jenis</code></td><td><i>(opsional)</i> <span class="inline">luar</span> atau <span class="inline">dalam</span></td></tr>
        </table>
        <pre><span class="c"># Contoh</span>
GET /api/dinas?tanggal=2025-08-24
GET /api/dinas?mulai=2025-08-01&selesai=2025-08-31
GET /api/dinas?tanggal=2025-08-24&jenis=luar</pre>
    </section>

    <section>
        <div class="ep-head">
            <h2 style="margin-bottom:4px"><span class="ico">🔍</span> Pencarian Surat Tugas</h2>
            <span class="method">GET</span><span class="path">/api/dinas/cari</span><span class="pill key">butuh API key</span>
        </div>
        <table>
            <tr><th>Parameter</th><th>Keterangan</th></tr>
            <tr><td><code>nip</code></td><td>Cari semua surat tugas milik NIP tersebut</td></tr>
            <tr><td><code>nomor</code></td><td>Cari berdasarkan <code>nomor_std</code> <b>ATAU</b> <code>nomor_spd</code></td></tr>
            <tr><td><code>tahun</code></td><td><i>(opsional)</i> format <span class="inline">YYYY</span></td></tr>
        </table>
        <div class="note">Wajib mengirim minimal salah satu: <code>nip</code> atau <code>nomor</code>.</div>
        <pre><span class="c"># Contoh</span>
GET /api/dinas/cari?nip=197401052001121001&tahun=2025
GET /api/dinas/cari?nomor=209/UN44/OT.02/2025</pre>
    </section>

    <section>
        <h2><span class="ico">📦</span> Format Response</h2>
        <pre>{
  <span class="c">"status"</span>: true,
  <span class="c">"jumlah"</span>: 112,
  <span class="c">"data"</span>: [
    {
      <span class="c">"nip"</span>: "197401052001121001",
      <span class="c">"nama"</span>: "Abdul Kadir Kamaluddin, SP., M.Si",
      <span class="c">"nomor_std"</span>: "1146/UN44/RT.11/2025",
      <span class="c">"nomor_spd"</span>: "1146/UN44/RT.11/2025",
      <span class="c">"kegiatan"</span>: "...",
      <span class="c">"tujuan"</span>: "Jakarta",
      <span class="c">"jenis"</span>: "luar_kota",
      <span class="c">"tanggal_mulai"</span>: "2025-08-24",
      <span class="c">"tanggal_selesai"</span>: "2025-08-26",
      <span class="c">"status"</span>: "terverifikasi"
    }
  ]
}</pre>
        <p style="font-size:13px;color:var(--muted);margin-top:6px">
            <code>tujuan</code> &amp; <code>nomor_spd</code> bernilai <span class="inline">null</span> untuk dinas dalam kota.
            <code>jenis</code> berisi <span class="inline">luar_kota</span> atau <span class="inline">dalam_kota</span>.</p>
    </section>

    <section>
        <h2><span class="ico">🚦</span> Kode Status</h2>
        <div class="status-row"><span class="code-num s200">200</span> Permintaan berhasil</div>
        <div class="status-row"><span class="code-num s401">401</span> API key tidak valid / tidak dikirim</div>
        <div class="status-row"><span class="code-num s422">422</span> Parameter tidak valid atau kurang</div>
    </section>

    <section>
        <h2><span class="ico">ℹ️</span> Catatan Penting</h2>
        <ul class="notes">
            <li>Pencocokan pegawai di sisi SIMPEG menggunakan <b>NIP</b>.</li>
            <li>Hanya surat berstatus <b>terverifikasi</b> (disetujui) yang ditampilkan.</li>
            <li>Data mencakup dinas <b>luar kota</b> (dari SPD) dan <b>dalam kota</b> sekaligus.</li>
            <li>Halaman panduan ini bersifat publik &mdash; <code>GET /api/dinas/panduan</code> (tanpa API key).</li>
        </ul>
    </section>

    <footer>
        &copy; {{ date('Y') }} UPT TIK &mdash; Universitas Khairun Ternate &middot; SPPD UNKHAIR API v1.0
    </footer>
</div>
</body>
</html>
