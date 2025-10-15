<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Permohonan Dana Perbaikan Aset</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .container { width: 90%; margin: 0 auto; }
        h2 { text-align: center; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 5px; }
        .label { width: 30%; }
        .colon { width: 5%; }
        .value { width: 65%; }
        .section { margin-top: 25px; }
        .signatures { margin-top: 50px; width: 100%; }
        .sig-col { width: 50%; text-align: center; float: left; }
    </style>
</head>
<body>
    <div class="container">
        <h2>PERMOHONAN DANA PERBAIKAN ASET</h2>

        <p>Pada hari ini, <?= esc($tanggal) ?>, kami yang bertanda tangan dibawah ini:</p>

        <div class="section">
            <strong>Pemohon</strong>
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($pemohon->full_name) ?></td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc(ucwords(str_replace('_', ' ', $pemohon->role))) ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <strong>Menyetujui</strong>
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($penyetuju_nama) ?></td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="colon">:</td>
                    <td class="value">(Jabatan atasan)</td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 20px;">Dengan ini Pemohon mengajukan permohonan dana perbaikan kepada pihak yang Menyetujui, dalam rangka menunjang dan memperlancar proses pelaksanaan pekerjaan dalam lingkup Bosowa Transportation.</p>

        <p>Adapun identitas Aset yang dimaksud sebagai berikut:</p>
        <table>
            <tr><td class="label">Jenis Aset</td><td class="colon">:</td><td class="value"><?= esc($aset['nama_kategori']) ?></td></tr>
            <tr><td class="label">Merk</td><td class="colon">:</td><td class="value"><?= esc($aset['nama_merk']) ?></td></tr>
            <tr><td class="label">Tipe</td><td class="colon">:</td><td class="value"><?= esc($aset['nama_tipe']) ?></td></tr>
            <tr><td class="label">Serial Number</td><td class="colon">:</td><td class="value"><?= esc($aset['serial_number']) ?></td></tr>
        </table>

        <div class="section">
            <strong>Keterangan Kerusakan:</strong>
            <p><?= nl2br(esc($kerusakan)) ?></p>
        </div>

        <div class="section">
            <strong>Estimasi Biaya Perbaikan:</strong>
            <p>Rp <?= number_format($biaya, 0, ',', '.') ?></p>
        </div>

        <p style="margin-top: 30px;">Demikian permohonan ini dibuat untuk ditindaklanjuti dan dipergunakan dengan penuh rasa tanggung jawab.</p>

        <div class="signatures">
            <div class="sig-col">
                <p>Menyetujui,</p>
                <br><br><br><br>
                <p>(<?= esc($penyetuju_nama) ?>)</p>
            </div>
            <div class="sig-col">
                <p>Pemohon,</p>
                <br><br><br><br>
                <p>(<?= esc($pemohon->full_name) ?>)</p>
            </div>
        </div>

    </div>
</body>
</html>