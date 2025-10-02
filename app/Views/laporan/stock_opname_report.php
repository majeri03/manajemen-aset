<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0">Laporan Siklus Stock Opname</h4>
        <p class="text-muted small">Lihat riwayat dan unduh laporan untuk setiap siklus yang telah selesai.</p>
    </div>
    <a href="<?= base_url('stockopname') ?>" class="btn btn-secondary">
        <i class="bi bi-speedometer2 me-2"></i>Kembali ke Dasbor SO
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="table-container shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Periode Siklus</th>
                    <th>Dimulai Oleh</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cycles)): $i = 1; ?>
                    <?php foreach ($cycles as $cycle): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <span class="fw-bold">
                                    <?= date('d M Y', strtotime($cycle['start_date'])) ?>
                                </span>
                                s/d
                                <span class="fw-bold">
                                    <?= date('d M Y', strtotime($cycle['end_date'])) ?>
                                </span>
                            </td>
                            <td><?= esc($cycle['started_by']) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('laporan/stockopname/export/' . $cycle['id']) ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-file-earmark-excel-fill me-2"></i>Export Laporan Excel
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <p class="text-muted">Belum ada siklus stock opname yang selesai.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>