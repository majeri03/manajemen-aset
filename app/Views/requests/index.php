<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Permintaan Perubahan Aset</h4>
    <p class="text-muted small">Setujui atau tolak perubahan data yang diajukan oleh pengguna.</p>
</div>

<div class="table-container shadow-sm">
    <ul class="nav nav-tabs" id="requestTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                Menunggu Persetujuan <span class="badge bg-danger"><?= count($pending) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                Riwayat
            </button>
        </li>
    </ul>

    <div class="tab-content" id="requestTabsContent">
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode Aset</th>
                            <th>Pengaju</th>
                            <th>Tanggal Diajukan</th>
                            <th>Detail Perubahan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pending)): ?>
                            <?php foreach ($pending as $req): ?>
                                <tr>
                                    <td><?= esc($req['kode']) ?></td>
                                    <td><?= esc($req['full_name']) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($req['created_at'])) ?></td>
                                    <td>
                                        <?php $proposed = json_decode($req['proposed_data'], true); ?>
                                        <ul>
                                            <?php foreach($proposed as $key => $value): ?>
                                                <li><strong><?= esc(ucfirst(str_replace('_', ' ', $key))) ?>:</strong> <?= esc($value) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <a href="/requests/approve/<?= $req['id'] ?>" class="btn btn-success btn-sm">Setujui</a>
                                        <a href="/requests/reject/<?= $req['id'] ?>" class="btn btn-danger btn-sm">Tolak</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Tidak ada permintaan yang menunggu persetujuan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode Aset</th>
                            <th>Pengaju</th>
                            <th>Tanggal Diajukan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $req): ?>
                                <tr>
                                    <td><?= esc($req['kode']) ?></td>
                                    <td><?= esc($req['full_name']) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($req['created_at'])) ?></td>
                                    <td>
                                        <?php if($req['status'] == 'approved'): ?>
                                            <span class="badge bg-success">Disetujui</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Belum ada riwayat permintaan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>