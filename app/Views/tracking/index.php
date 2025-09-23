<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Asset Command Center
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Asset Command Center</h4>
    <p class="text-muted small">Linimasa semua aktivitas dan perubahan aset secara real-time.</p>
</div>

<div class="row g-4 mb-4">
    </div>

<div class="table-container shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Linimasa Aktivitas</h5>
        <div class="input-group" style="width: 250px;">
            <input type="text" id="timelineSearch" class="form-control" placeholder="Cari kode aset...">
            <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
        </div>
    </div>

    <div class="timeline-container py-0">
        <ul class="timeline">
            <?php if (!empty($activities)): ?>
                <?php foreach($activities as $activity): ?>
                    <li class="timeline-item">
                        <?php
                            $badgeClass = 'bg-info'; // Default
                            $iconClass = 'bi-info-lg'; // Default
                            if ($activity['tipe_aktivitas'] == 'create') {
                                $badgeClass = 'bg-success';
                                $iconClass = 'bi-plus-lg';
                            } elseif ($activity['tipe_aktivitas'] == 'update') {
                                $badgeClass = 'bg-warning';
                                $iconClass = 'bi-pencil-fill';
                            } elseif ($activity['tipe_aktivitas'] == 'delete') {
                                $badgeClass = 'bg-danger';
                                $iconClass = 'bi-trash-fill';
                            }
                        ?>
                        <div class="timeline-badge <?= $badgeClass ?>">
                            <i class="bi <?= $iconClass ?>"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h5 class="timeline-title"><?= esc($activity['kode_aset']) ?></h5>
                                <p><small class="text-muted"><i class="bi bi-clock-fill"></i> <?= date('d F Y, H:i', strtotime($activity['tanggal'])) ?> oleh <?= esc($activity['nama_user']) ?></small></p>
                            </div>
                            <div class="timeline-body">
                                <?php if($activity['tipe_aktivitas'] == 'create'): ?>
                                    <p>Aset baru berhasil ditambahkan ke dalam sistem.</p>
                                <?php elseif($activity['tipe_aktivitas'] == 'delete'): ?>
                                    <p class="text-danger fw-bold">Aset telah dihapus dari sistem.</p>
                                <?php else: ?>
                                    <p class="mb-2">Data aset telah diperbarui:</p>
                                    <div class="change-grid">
                                        <?php 
                                            $changes = json_decode($activity['proposed_data'], true);
                                            if (is_array($changes)):
                                                foreach($changes as $key => $value): 
                                        ?>
                                        <div class="change-item">
                                            <strong class="change-key"><?= esc(ucfirst(str_replace('_', ' ', $key))) ?></strong>
                                            <span class="change-value"><?= esc($value) ?></span>
                                        </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                 <div class="text-center p-5"><p class="text-muted fs-5">Belum ada aktivitas.</p></div>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('timelineSearch');
    const timelineItems = document.querySelectorAll('.timeline-item');

    searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        timelineItems.forEach(item => {
            const title = item.querySelector('.timeline-title').textContent.toLowerCase();
            if (title.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
<?= $this->endSection() ?>