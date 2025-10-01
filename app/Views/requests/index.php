<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Persetujuan Perubahan Aset</h4>
    <p class="text-muted small">Setujui atau tolak perubahan data yang diajukan oleh pengguna.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="chart-nav-container">
    <ul class="nav nav-pills chart-nav" id="requestTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                <i class="bi bi-hourglass-split"></i> Menunggu Persetujuan 
                <span class="badge bg-danger ms-2"><?= count($pending) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                <i class="bi bi-clock-history"></i> Riwayat
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="requestTabsContent">
    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
        <div class="row g-4">
            <?php if (!empty($pending)): ?>
                <?php foreach ($pending as $req): ?>
                    <div class="col-lg-6">
                        <div class="card shadow-sm request-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary"><?= esc($req['kode']) ?></h5>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($req['created_at'])) ?></small>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-2">Diajukan oleh: <strong><?= esc($req['full_name']) ?></strong></p>
                                <h6 class="card-subtitle mb-2">Detail Perubahan:</h6>
                                <?php $proposed = json_decode($req['proposed_data'], true); ?>
                                <ul>
                                    <?php foreach($proposed as $key => $value): ?>
                                        <li><strong><?= esc(ucfirst(str_replace('_', ' ', $key))) ?>:</strong> <?= esc($value) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="card-footer text-end bg-transparent border-0 pt-0">
                                <a href="javascript:void(0)" onclick="confirmAction(event, 'approve', <?= $req['id'] ?>)" class="btn btn-success"><i class="bi bi-check-lg"></i> Setujui</a>
                                <a href="javascript:void(0)" onclick="confirmAction(event, 'reject', <?= $req['id'] ?>)" class="btn btn-danger"><i class="bi bi-x-lg"></i> Tolak</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light text-center">
                        <i class="bi bi-check2-circle fs-3"></i>
                        <h5 class="mt-2">Tidak Ada Permintaan</h5>
                        <p class="text-muted">Semua permintaan perubahan data aset sudah diproses.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
        <div class="row g-4">
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $req): ?>
                    <div class="col-lg-6">
                        <div class="card shadow-sm request-card h-100">
                             <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?= esc($req['kode']) ?></h5>
                                <?php if($req['status'] == 'approved'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Disetujui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Ditolak</span>
                                <?php endif; ?>
                            </div>
                             <div class="card-body">
                                <p class="small text-muted mb-2">Diajukan oleh: <strong><?= esc($req['full_name']) ?></strong> pada <?= date('d M Y, H:i', strtotime($req['created_at'])) ?></p>
                                <h6 class="card-subtitle mb-2">Detail Perubahan yang Diajukan:</h6>
                                <?php $proposed = json_decode($req['proposed_data'], true); ?>
                                <ul>
                                    <?php foreach($proposed as $key => $value): ?>
                                        <li><strong><?= esc(ucfirst(str_replace('_', ' ', $key))) ?>:</strong> <?= esc($value) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center mt-4">
                    <p class="text-muted">Belum ada riwayat permintaan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmAction(event, action, id) {
        event.preventDefault();

        const isApprove = action === 'approve';
        const config = {
            title: isApprove ? 'Setujui Perubahan?' : 'Tolak Perubahan?',
            text: "Tindakan ini akan memperbarui data aset secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#198754' : '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isApprove ? 'Ya, Setujui!' : 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        };
        
        if (!isApprove) {
            config.text = "Anda yakin ingin menolak permintaan perubahan data ini?";
        }

        Swal.fire(config).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('requests/') ?>${action}/${id}`;
            }
        });
    }
</script>
<?= $this->endSection() ?>