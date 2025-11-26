<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pemusnahan Aset</title>
    <style>
        /* Copy style dari pdf lain (font, margin, dll) */
        body { font-family: 'Times New Roman', serif; font-size: 12pt; }
        .header { text-align: center; font-weight: bold; margin-bottom: 30px; }
        .content { text-align: justify; margin-top: 20px; }
        table { width: 100%; margin-top: 10px; }
        td { vertical-align: top; padding: 5px; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="header">
        <h3>BERITA ACARA PEMUSNAHAN ASET</h3>
    </div>

    <div class="content">
        <p>Pada hari ini, <strong><?= date('l, d F Y') ?></strong>, telah dilakukan pemusnahan aset perusahaan karena kondisi rusak berat / tidak memiliki nilai ekonomis, dengan rincian:</p>

        <table>
            <tr><td width="30%">Kode Aset</td><td>: <strong><?= esc($aset['kode']) ?></strong></td></tr>
            <tr><td>Nama Aset</td><td>: <?= esc($aset['nama_sub_kategori']) ?> <?= esc($aset['nama_merk']) ?></td></tr>
            <tr><td>Serial Number</td><td>: <?= esc($aset['serial_number']) ?></td></tr>
            <tr><td>Lokasi Terakhir</td><td>: <?= esc($aset['nama_lokasi']) ?></td></tr>
            <tr><td>Nilai Perolehan</td><td>: Rp <?= number_format($aset['harga_beli'] ?? 0, 0, ',', '.') ?></td></tr>
        </table>

        <p>Dengan diterbitkannya berita acara ini, maka aset tersebut dinyatakan telah dihapus dari daftar inventaris aktif perusahaan (Write-off).</p>
        
        <br>
        <p><strong>Penyetuju Pemusnahan:</strong></p>
        <p><?= esc($user_pelaksana->full_name) ?> (<?= esc($user_pelaksana->role) ?>)</p>
    </div>
</body>
</html>