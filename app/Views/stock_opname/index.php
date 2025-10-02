<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="mb-0">Dasbor Stock Opname</h4>
        <?php if ($activeCycle): ?>
            <p class="text-muted small mb-0">
                Siklus Aktif Dimulai: <strong><?= date('d F Y, H:i', strtotime($activeCycle->start_date)) ?></strong>
            </p>
        <?php else: ?>
            <p class="text-muted small mb-0">Belum ada siklus yang dimulai. Tekan tombol untuk memulai.</p>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('laporan/stockopname') ?>" class="btn btn-info">
            <i class="bi bi-archive-fill me-2"></i>Lihat Laporan Siklus
        </a>
        <form action="<?= base_url('stockopname/start-cycle') ?>" method="post" onsubmit="return confirmStartCycle(event);" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-arrow-repeat me-2"></i>Mulai Siklus Baru
            </button>
        </form>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Bagian Statistik Progres -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Progres Siklus Saat Ini</h5>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= round($progress, 2) ?>%;" aria-valuenow="<?= round($progress, 2) ?>" aria-valuemin="0" aria-valuemax="100"><?= round($progress, 2) ?>%</div>
                </div>
                <div class="d-flex justify-content-between mt-2 text-muted small">
                    <span>Sudah Dicek: <strong><?= $sudahDicek ?></strong></span>
                    <span>Belum Dicek: <strong><?= $belumDicek ?></strong></span>
                    <span>Total Aset: <strong><?= $totalAset ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian Filter dan Daftar Aset -->
<div class="table-container shadow-sm">
    <h5 class="mb-3">Daftar Aset dalam Siklus</h5>
    <form action="<?= base_url('stockopname') ?>" method="get" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="lokasi_id" class="form-label">Filter Lokasi</label>
            <select name="lokasi_id" id="lokasi_id" class="form-select">
                <option value="">Semua Lokasi</option>
                <?php foreach ($lokasi_list as $lokasi) : ?>
                    <option value="<?= esc($lokasi['id']) ?>" <?= ($filters['lokasi_id'] ?? '') == $lokasi['id'] ? 'selected' : '' ?>>
                        <?= esc($lokasi['nama_lokasi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="status_verifikasi" class="form-label">Filter Status</label>
            <select name="status_verifikasi" id="status_verifikasi" class="form-select">
                <option value="">Semua Status</option>
                <option value="Belum Dicek" <?= ($filters['status_verifikasi'] ?? '') == 'Belum Dicek' ? 'selected' : '' ?>>Belum Dicek</option>
                <option value="Sudah Dicek" <?= ($filters['status_verifikasi'] ?? '') == 'Sudah Dicek' ? 'selected' : '' ?>>Sudah Dicek</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="kode" class="form-label">Cari Kode Aset</label>
            <input type="text" name="kode" class="form-control" placeholder="Masukkan sebagian atau seluruh kode aset..." value="<?= esc($filters['kode'] ?? '') ?>">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Kode Aset</th>
                    <th>Sub Kategori</th>
                    <th>Merk</th>
                    <th>Lokasi</th>
                    <th>Status Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($asetList)): ?>
                    <?php foreach ($asetList as $item): ?>
                        <tr>
                            <td><?= esc($item['kode']) ?></td>
                            <td><?= esc($item['nama_sub_kategori']) ?></td>
                            <td><?= esc($item['nama_merk']) ?></td>
                            <td><?= esc($item['nama_lokasi']) ?></td>
                            <td>
                                <?php if ($item['status_verifikasi'] == 'Sudah Dicek'): ?>
                                    <span class="badge bg-success">Sudah Dicek</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Belum Dicek</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted">Tidak ada data aset untuk filter yang dipilih.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmStartCycle(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Mulai Siklus Stock Opname Baru?',
            text: "Ini akan menyelesaikan siklus yang sedang berjalan (jika ada) dan mereset status verifikasi SEMUA aset menjadi 'Belum Dicek'. Anda yakin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Mulai Siklus Baru!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
        return false;
    }
</script>
<?= $this->endSection() ?>
