<!DOCTYPE html>
<html>
<head>
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Detail Aset</h3>
            </div>
            <div class="card-body">
                <?php if ($aset): ?>
                    <p><strong>Kode:</strong> <?= esc($aset['kode']) ?></p>
                    <p><strong>Kategori:</strong> <?= esc($aset['kategori']) ?></p>
                    <p><strong>Merk:</strong> <?= esc($aset['merk']) ?></p>
                    <p><strong>Serial Number:</strong> <?= esc($aset['serial_number']) ?></p>
                    <p><strong>Tahun:</strong> <?= esc($aset['tahun']) ?></p>
                    <p><strong>Lokasi Terdaftar:</strong> <?= esc($aset['lokasi']) ?></p>
                    <p><strong>Kondisi:</strong> <?= esc($aset['status']) ?></p>
                    <p><strong>Keterangan:</strong> <?= esc($aset['keterangan']) ?></p>
                <?php else: ?>
                    <p class="text-danger">Aset tidak ditemukan.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="<?= base_url('/login') ?>" class="btn btn-primary">Login untuk Mengubah Data</a>
            </div>
        </div>
    </div>
</body>
</html>