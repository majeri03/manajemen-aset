<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Asset Intelligence Hub
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="main-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0">Asset Intelligence Hub</h4>
        <p class="text-muted small">Analisis komprehensif dan tren data aset Anda.</p>
    </div>
    <a href="<?= base_url('aset/laporan/export') ?>" class="btn btn-success shadow-sm">
        <i class="bi bi-file-earmark-excel-fill me-2"></i>Unduh Laporan Lengkap
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="report-card-v2 card-v2-total">
                    <i class="bi bi-box-seam card-v2-icon"></i>
                    <p>Total Aset</p>
                    <h2><?= esc($totalAset) ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card-v2 card-v2-baik">
                    <i class="bi bi-shield-check card-v2-icon"></i>
                    <p>Kondisi Baik</p>
                    <h2><?= esc($asetBaik) ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card-v2 card-v2-rusak">
                    <i class="bi bi-wrench-adjustable-circle card-v2-icon"></i>
                    <p>Aset Rusak</p>
                    <h2><?= esc($asetRusak) ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card-v2 card-v2-lainnya">
                    <i class="bi bi-archive card-v2-icon"></i>
                    <p>Tidak Terpakai</p>
                    <h2><?= esc($asetTidakTerpakai) ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Top 5 Lokasi Aset</h5>
            <ul class="list-group list-group-flush">
                <?php foreach($lokasiAset as $lokasi): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-geo-alt-fill me-2 text-primary"></i><?= esc($lokasi['lokasi']) ?></span>
                    <span class="badge bg-primary rounded-pill"><?= esc($lokasi['jumlah']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="table-container shadow-sm">
            <h5 class="mb-3">Tren Penambahan Aset (6 Bulan Terakhir)</h5>
            <div class="chart-container-laporan" style="height: 280px;">
                <canvas id="assetTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="table-container shadow-sm mt-4">
    <h5 class="mb-3">Riwayat Unduhan Laporan</h5>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Diunduh Oleh</th>
                    <th>Tanggal Unduh</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($riwayat)): ?>
                    <?php foreach ($riwayat as $item): ?>
                        <tr>
                            <td>
                                <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                                <?= esc($item['nama_file']) ?>
                            </td>
                            <td><?= esc($item['full_name']) ?></td>
                            <td><?= date('d F Y, H:i', strtotime($item['created_at'])) ?> WIB</td>
                            <td class="text-end">
                                <a href="<?= base_url('laporan/download/' . $item['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download me-1"></i> Unduh Ulang
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            Belum ada riwayat unduhan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>



<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk Chart Tren
    const trendLabels = <?= json_encode($trendLabels) ?>;
    const trendData = <?= json_encode($trendData) ?>;

    const ctx = document.getElementById('assetTrendChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(61, 162, 255, 0.6)');
    gradient.addColorStop(1, 'rgba(61, 162, 255, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Aset Baru',
                data: trendData,
                borderColor: '#3da2ff',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3da2ff',
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#3da2ff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>