<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Permohonan Dana Perbaikan Aset</title>
    <style>
        @page { margin: 30px 40px; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; }
        .header { position: fixed; top: 0px; left: 0px; right: 0px; height: 60px; }
        .aksen { position: absolute; top: 0; left: 0; width: 80px; }
        .logo { position: absolute; top: 10px; right: 0; width: 180px; }
        .container { width: 100%; margin-top: 110px; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 30px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding-bottom: 2px; vertical-align: top; }
        .content { margin-top: 20px; text-align: justify; }
        .details-table { width: 100%; margin-top: 15px; margin-left: 25px; }
        .details-table td { padding: 4px 0; }
        .signatures { margin-top: 50px; width: 100%; }
        .signature-col { width: 45%; float: left; text-align: center; }
        .signature-col.right { float: right; }
        .clear { clear: both; }
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
        <h3 class="title">PERMOHONAN DANA PERBAIKAN ASET</h3>
        <p>Pada hari ini, <?= date('l, d F Y') ?>, kami yang bertanda tangan dibawah ini:</p>

        <table class="info-table">
            <tr>
                <td width="20%"><strong>Pemohon</strong></td>
                <td></td>
            </tr>
            <tr>
                <td style="padding-left: 15px;">Nama</td>
                <td>: <?= esc($pemohon['nama_karyawan'] ?? '(Tidak Diketahui)') ?></td>
            </tr>
            <tr>
                <td style="padding-left: 15px;">Jabatan</td>
                <td>: <?= esc($pemohon['jabatan'] ?? 'Staff') ?></td>
            </tr>
            <tr><td colspan="2" style="height: 15px;"></td></tr>
            <tr>
                <td><strong>Menyetujui</strong></td>
                <td></td>
            </tr>
            <tr>
                <td style="padding-left: 15px;">Nama</td>
                <td>: <?= esc($penyetuju['nama'] ?? '(Belum Ditentukan)') ?></td>
            </tr>
            <tr>
                <td style="padding-left: 15px;">Jabatan</td>
                <td>: <?= esc($penyetuju['jabatan'] ?? 'Atasan') ?></td>
            </tr>
        </table>

        <div class="content">
            <p>Dengan ini Pemohon mengajukan permohonan dana perbaikan kepada pihak yang Menyetujui, dalam rangka menunjang dan memperlancar proses pelaksanaan pekerjaan dalam lingkup Bosowa Transportation.</p>
            <p>Adapun identitas Aset yang dimaksud sebagai berikut:</p>
            <table class="details-table">
                <tr><td width="25%">Jenis Aset</td><td>: <?= esc($aset['nama_sub_kategori'] ?? '') ?></td></tr>
                <tr><td>Merk</td><td>: <?= esc($aset['nama_merk'] ?? '') ?></td></tr>
                <tr><td>Type</td><td>: <?= esc($aset['nama_tipe'] ?? '') ?></td></tr>
                <tr><td>Serial Number</td><td>: <?= esc($aset['serial_number'] ?? '') ?></td></tr>
            </table>

            <h4 style="margin-top: 25px; margin-bottom: 5px;">Keterangan Kerusakan:</h4>
            <p style="margin-top: 0;"><?= esc($perbaikan['keterangan_kerusakan'] ?? $kerusakan ?? '-') ?></p>

            <h4 style="margin-top: 25px; margin-bottom: 5px;">Estimasi Biaya Perbaikan:</h4>
            <p style="margin-top: 0;">Rp <?= number_format($perbaikan['estimasi_biaya'] ?? $estimasi_biaya ?? 0, 0, ',', '.') ?></p>

            <p style="margin-top: 25px;">Demikian permohonan ini dibuat untuk ditindaklanjuti dan dipergunakan dengan penuh rasa tanggung jawab.</p>
        </div>

        <div class="signatures">
            <div class="signature-col">
                <p style="margin-bottom: 0;">Menyetujui,</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0;"><strong><u><?= esc($penyetuju['nama'] ?? '.........................') ?></u></strong></p>
                <p style="margin: 0;"><?= esc($penyetuju['jabatan'] ?? 'Atasan') ?></p>
            </div>
            <div class="signature-col right">
                <p style="margin-bottom: 0;">Pemohon,</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0;"><strong><u><?= esc($pemohon['nama_karyawan'] ?? '.........................') ?></u></strong></p>
                <p style="margin: 0;"><?= esc($pemohon['jabatan'] ?? 'Staff') ?></p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>