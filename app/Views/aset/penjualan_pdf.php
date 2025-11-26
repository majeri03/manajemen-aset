<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penjualan Aset</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; }
        .header { position: fixed; top: 0px; left: 0px; right: 0px; height: 60px; }
        .aksen { position: absolute; top: 0; left: 0; width: 80px; }
        .logo { position: absolute; top: 10px; right: 0; width: 180px; }
        .container { width: 100%; margin-top: 110px; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 30px; }
        .content { margin-top: 20px; text-align: justify; }
        .details-table { width: 100%; margin-top: 15px; margin-left: 25px; margin-bottom: 20px; }
        .details-table td { padding: 4px 0; vertical-align: top; }
        .signatures { margin-top: 50px; width: 100%; }
        .signature-col { width: 45%; float: left; text-align: center; }
        .signature-col.right { float: right; }
        .clear { clear: both; }
        .price-box { border: 2px solid #000; padding: 10px; text-align: center; margin: 20px 0; font-weight: bold; font-size: 14pt; }
    </style>
</head>
<body>
    <?php
        $pathLogo = FCPATH . 'assets/images/logo_bosowa.png';
        $base64Logo = is_readable($pathLogo) ? 'data:' . mime_content_type($pathLogo) . ';base64,' . base64_encode(file_get_contents($pathLogo)) : '';
        $pathAksen = FCPATH . 'assets/images/aksen_bosowa.png';
        $base64Aksen = is_readable($pathAksen) ? 'data:' . mime_content_type($pathAksen) . ';base64,' . base64_encode(file_get_contents($pathAksen)) : '';
    ?>
    <div class="header">
        <?php if ($base64Aksen): ?><img src="<?= $base64Aksen ?>" alt="Aksen" class="aksen"><?php endif; ?>
        <?php if ($base64Logo): ?><img src="<?= $base64Logo ?>" alt="Logo" class="logo"><?php endif; ?>
    </div>

    <div class="container">
        <h3 class="title">BERITA ACARA PENJUALAN ASET</h3>

        <div class="content">
            <p>Pada hari ini, <strong><?= date('l, d F Y', strtotime($tanggal)) ?></strong>, telah dilakukan penjualan/pelepasan aset perusahaan dengan rincian sebagai berikut:</p>

            <table class="details-table">
                <tr><td width="30%">Kode Aset</td><td>: <strong><?= esc($aset['kode']) ?></strong></td></tr>
                <tr><td>Kategori / Sub</td><td>: <?= esc($aset['nama_kategori']) ?> / <?= esc($aset['nama_sub_kategori']) ?></td></tr>
                <tr><td>Merk / Tipe</td><td>: <?= esc($aset['nama_merk']) ?> / <?= esc($aset['nama_tipe'] ?? '-') ?></td></tr>
                <tr><td>Serial Number</td><td>: <?= esc($aset['serial_number'] ?? '-') ?></td></tr>
                <tr><td>Tahun Beli</td><td>: <?= esc($aset['tahun_beli']) ?></td></tr>
                <tr><td>Keterangan Tambahan</td><td>: <?= esc($aset['keterangan'] ?? '-') ?></td></tr>
            </table>

            <p>Aset tersebut di atas telah dijual dengan harga:</p>
            
            <div class="price-box">
                Rp <?= number_format($harga_jual, 0, ',', '.') ?>,-
            </div>

            <p>Dengan ditandatanganinya berita acara ini, maka status kepemilikan dan tanggung jawab atas aset tersebut telah berpindah tangan dan tidak lagi menjadi inventaris perusahaan.</p>
        </div>

        <div class="signatures">
            <div class="signature-col">
                <p style="margin-bottom: 0;">Pihak Penjual</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0;"><strong><u><?= esc($penjual->full_name ?? 'Admin Asset') ?></u></strong></p>
                <p style="margin: 0;">(Perwakilan Manajemen)</p>
            </div>
            <div class="signature-col right">
                <p style="margin-bottom: 0;">Pihak Pembeli</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0;"><strong>_______________________</strong></p>
                <p style="margin: 0;">(Tanda Tangan & Nama Jelas)</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>