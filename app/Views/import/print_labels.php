<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .print-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .label-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        .label {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .label .kode-aset {
            font-weight: bold;
            font-size: 14px;
            margin: 0;
            word-wrap: break-word;
        }
        .label .detail-aset {
            font-size: 11px;
            color: #555;
            margin: 4px 0;
            word-wrap: break-word;
        }
        .label img {
            width: 100px;
            height: 100px;
            margin-top: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #fff;
            }
            .print-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .label-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .label {
                page-break-inside: avoid;
                width: 54mm; /* Sesuaikan dengan lebar stiker Postek Anda */
                height: 30mm; /* Sesuaikan dengan tinggi stiker Postek Anda */
                border: 1px dashed #999;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
             .label img { width: 60px; height: 60px; }
             .label .kode-aset { font-size: 8pt; }
             .label .detail-aset { font-size: 6pt; }
        }
    </style>
</head>
<body>

    <div class="print-container">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h4 class="mb-0">Pratinjau Cetak Label</h4>
                <p class="text-muted mb-0">Total <?= count($asets) ?> label akan dicetak.</p>
            </div>
            <div>
                <a href="<?= base_url('/aset') ?>" class="btn btn-secondary"><i class="bi bi-check-all me-2"></i>Selesai & Kembali</a>
                <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer-fill me-2"></i>Cetak Semua Label</button>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success no-print"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="label-grid">
            <?php if (!empty($asets)): ?>
                <?php foreach ($asets as $aset): ?>
                    <div class="label">
                        <p class="kode-aset"><?= esc($aset['kode']) ?></p>
                        <p class="detail-aset"><?= esc($aset['entitas_pembelian']) ?> - <?= esc($aset['nama_sub_kategori']) ?></p>
                        <img src="<?= base_url($aset['qrcode']) ?>" alt="QR Code">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada data aset untuk ditampilkan.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>