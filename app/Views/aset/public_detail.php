<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Detail Aset</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($aset): ?>
                    <p><strong>Kode:</strong> <?= esc($aset['kode']) ?></p>
                    <p><strong>Kategori Barang:</strong> <?= esc($aset['kategori']) ?></p>
                    <p><strong>Merk:</strong> <?= esc($aset['merk']) ?></p>
                    <p><strong>Serial Number:</strong> <?= esc($aset['serial_number']) ?></p>
                    <p><strong>Tahun:</strong> <?= esc($aset['tahun']) ?></p>
                    <p><strong>Lokasi Terdaftar:</strong> <?= esc($aset['lokasi']) ?></p>
                    <p><strong>Kondisi:</strong> <?= esc($aset['status']) ?></p>
                    <p><strong>Keterangan:</strong> <?= esc($aset['keterangan']) ?></p>
                    <hr>
                    <div class="text-center">
                        <img src="<?= base_url($aset['qrcode']) ?>" alt="QR Code Aset" class="img-fluid" style="max-width: 200px;">
                    </div>
                <?php else: ?>
                    <p class="text-danger">Aset tidak ditemukan.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="<?= base_url('/login') ?>" class="btn btn-secondary">Login ke Sistem</a>
            </div>
        </div>
    </div>
</body>
</html>