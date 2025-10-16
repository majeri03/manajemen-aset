<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Serah Terima</title>
    <style>
        @page { 
            margin: 30px 40px;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
        }
        .header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: 0px;
            height: 60px;
        }
        .aksen {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
        }
        .logo {
            position: absolute;
            top: 10px;
            right: 0;
            width: 180px;
        }
        .container {
            width: 100%;
            margin-top: 110px;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding-bottom: 2px;
        }
        .content {
            margin-top: 20px;
            text-align: justify;
        }
        .details-table {
            width: 100%;
            margin-top: 15px;
            margin-left: 25px;
        }
        .details-table td {
            padding: 4px 0;
        }
        /* ▼▼▼ CSS BARU UNTUK TANDA TANGAN ▼▼▼ */
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-col {
            width: 45%; /* Kurang dari 50% untuk menghindari masalah wrapping */
            float: left;
            text-align: center; /* Teks di dalam kolom tetap di tengah */
        }
        .signature-col.right {
            float: right;
        }
        .clear {
            clear: both;
        }
        /* ▲▲▲ AKHIR CSS BARU ▲▲▲ */
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
        <h3 class="title">BERITA ACARA SERAH TERIMA</h3>

        <p>Pada hari ini, <?= date('l, d F Y') ?>, kami yang bertanda tangan dibawah ini:</p>

        <table class="info-table">
            <tr>
                <td width="20%">Nama</td>
                <td>: <?= esc($pihak_pertama['nama_karyawan'] ?? '(Belum Ada)') ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: HCGA</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 5px;">Disebut sebagai <strong>Pihak Pertama</strong></td>
            </tr>
            <tr><td colspan="2" style="height: 15px;"></td></tr>
            <tr>
                <td>Nama</td>
                <td>: <?= esc($pihak_kedua['nama_karyawan']) ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: <?= esc($pihak_kedua['jabatan']) ?></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 5px;">Disebut sebagai <strong>Pihak Kedua</strong></td>
            </tr>
        </table>

        <div class="content">
            <p>Dengan ini menyatakan:</p>
            <p>Dalam rangka menunjang dan mempercepat proses pelaksanaan pekerjaan dalam lingkup Bosowa Transportation, maka pihak pertama meminjamkan Aset kepada pihak kedua untuk digunakan untuk keperluan tersebut. Adapun identitas Aset sebagai berikut:</p>

            <table class="details-table">
                <tr>
                    <td width="25%">Jenis Aset</td>
                    <td>: <?= esc($aset['nama_sub_kategori'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Merk</td>
                    <td>: <?= esc($aset['nama_merk'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Type</td>
                    <td>: <?= esc($aset['nama_tipe'] ?? '') ?></td>
                </tr>
                <tr>
                    <td>Serial Number</td>
                    <td>: <?= esc($aset['serial_number'] ?? '') ?></td>
                </tr>
            </table>

            <p style="margin-top: 20px;">Demikian berita acara serah terima ini dibuat dan ditandatangani Bersama-sama untuk menjadi pegangan dan dipergunakan sebaik-baiknya dengan penuh rasa tanggung jawab.</p>
        </div>

        <div class="signatures">
            <div class="signature-col">
                <p style="margin-bottom: 0;">Pihak Pertama,</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0;"><strong> <u><?= esc($pihak_pertama['nama_karyawan'] ?? '.........................') ?></u> </strong></p>
                <p style="margin: 0;">HCGA</p>
            </div>
            <div class="signature-col right">
                <p style="margin-bottom: 0;">Pihak Kedua,</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0;"><strong> <u><?= esc($pihak_kedua['nama_karyawan']) ?></u> </strong></p>
                <p style="margin: 0;"><?= esc($pihak_kedua['jabatan']) ?></p>
            </div>
            <div class="clear"></div>
        </div>
        </div>
</body>
</html>