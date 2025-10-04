<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0"><?= esc($title) ?></h4>
    <p class="text-muted small">Lokasi Audit: <strong><?= esc($lokasi_nama) ?></strong></p>
</div>

<div class="chart-nav-container">
    <ul class="nav nav-pills chart-nav" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ditemukan-tab" data-bs-toggle="tab" data-bs-target="#ditemukan-content" type="button" role="tab">
                <i class="bi bi-clipboard2-check me-2"></i>Ditemukan 
                <span class="badge rounded-pill bg-success ms-2"><?= count($found_in_place) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="salah-tempat-tab" data-bs-toggle="tab" data-bs-target="#salah-tempat-content" type="button" role="tab">
                <i class="bi bi-sign-wrong-location-fill me-2"></i>Lokasi Tidak Sesuai 
                <span class="badge rounded-pill bg-warning text-dark ms-2"><?= count($found_misplaced) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tidak-ditemukan-tab" data-bs-toggle="tab" data-bs-target="#tidak-ditemukan-content" type="button" role="tab">
                <i class="bi bi-archive-fill me-2"></i>Tidak Ditemukan 
                <span class="badge rounded-pill bg-danger ms-2"><?= count($not_found_assets) ?></span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="reportTabsContent">

    <div class="tab-pane fade show active" id="ditemukan-content" role="tabpanel">
        <div class="table-container shadow-sm">
            <div class="table-responsive" style="max-height: 450px;">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode Aset</th>
                            <th>Status Baru</th>
                            <th>Catatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($found_in_place)): ?>
                            <?php foreach ($found_in_place as $index => $aset): ?>
                                <tr id="row-found-<?= esc($aset['id']) ?>">
                                    <td class="fw-bold kode-text"><?= esc($aset['kode']) ?></td>
                                    <td><span class="badge bg-light text-dark status-text"><?= esc($aset['status']) ?></span></td>
                                    <td class="keterangan-text"><?= esc($aset['keterangan'] ?: '-') ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editReportModal"
                                                data-asset-id="<?= esc($aset['id']) ?>"
                                                data-type="found">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center p-5"><i class="bi bi-emoji-smile fs-3 text-muted"></i><p class="mt-2 text-muted">Tidak ada aset yang diverifikasi di lokasi ini.</p></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="salah-tempat-content" role="tabpanel">
        <div class="table-container shadow-sm">
            <div class="table-responsive" style="max-height: 450px;">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode Aset</th>
                            <th>Lokasi Seharusnya</th>
                            <th>Status Baru</th>
                            <th>Catatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if (!empty($found_misplaced)): ?>
                            <?php foreach ($found_misplaced as $index => $aset): ?>
                                <tr id="row-misplaced-<?= esc($aset['id']) ?>">
                                    <td class="fw-bold kode-text"><?= esc($aset['kode']) ?></td>
                                    <td><span class="badge bg-danger"><?= esc($aset['actual_location'] ?? 'N/A') ?></span></td>
                                    <td><span class="badge bg-light text-dark status-text"><?= esc($aset['status']) ?></span></td>
                                    <td class="keterangan-text"><?= esc($aset['keterangan'] ?: '-') ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning btn-edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editReportModal"
                                                data-asset-id="<?= esc($aset['id']) ?>"
                                                data-type="misplaced">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <tr><td colspan="5" class="text-center p-5"><i class="bi bi-emoji-smile fs-3 text-muted"></i><p class="mt-2 text-muted">Tidak ada aset salah tempat yang ditemukan.</p></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="tidak-ditemukan-content" role="tabpanel">
        </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="<?= base_url('scan-cepat') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali & Batalkan</a>
    <form action="<?= base_url('stockopname/process-scan-report') ?>" method="post" onsubmit="return confirm('Anda yakin ingin mengirim laporan ini ke server?');">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-send-fill me-2"></i>Kirim Finalisasi</button>
    </form>
</div>

<div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReportModalLabel">Edit Detail Aset: <span id="edit-modal-asset-code"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-modal-asset-id">
                <input type="hidden" id="edit-modal-asset-type">

                <div class="mb-3">
                    <label for="edit-modal-status" class="form-label">Ubah Status/Kondisi</label>
                    <select class="form-select" id="edit-modal-status" name="status">
                        <option value="Baik Terpakai">Baik (Terpakai)</option>
                        <option value="Baik Tidak Terpakai">Baik (Tidak Terpakai)</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit-modal-keterangan" class="form-label">Ubah Catatan</label>
                    <textarea class="form-control" id="edit-modal-keterangan" name="keterangan" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="edit-modal-save-button" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editReportModalEl = document.getElementById('editReportModal');
    const editReportModal = new bootstrap.Modal(editReportModalEl);
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

    // Event saat modal edit akan ditampilkan
    editReportModalEl.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const assetId = button.getAttribute('data-asset-id');
        const type = button.getAttribute('data-type');
        const row = document.getElementById(`row-${type}-${assetId}`);

        const kode = row.querySelector('.kode-text').textContent;
        const status = row.querySelector('.status-text').textContent;
        const keterangan = row.querySelector('.keterangan-text').textContent;

        // Isi form di dalam modal
        document.getElementById('edit-modal-asset-id').value = assetId;
        document.getElementById('edit-modal-asset-type').value = type;
        document.getElementById('edit-modal-asset-code').textContent = kode;
        document.getElementById('edit-modal-status').value = status;
        document.getElementById('edit-modal-keterangan').value = (keterangan === '-') ? '' : keterangan;
    });

    // Event saat tombol "Simpan Perubahan" di modal di-klik
    document.getElementById('edit-modal-save-button').addEventListener('click', function() {
        const assetId = document.getElementById('edit-modal-asset-id').value;
        const type = document.getElementById('edit-modal-asset-type').value;
        const newStatus = document.getElementById('edit-modal-status').value;
        const newKeterangan = document.getElementById('edit-modal-keterangan').value;
        
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        const formData = new URLSearchParams();
        formData.append(csrfName, csrfHash);
        formData.append('asset_id', assetId);
        formData.append('type', type);
        formData.append('status', newStatus);
        formData.append('keterangan', newKeterangan);

        fetch("<?= base_url('stockopname/update-session-report') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Perbarui tampilan di tabel secara langsung
                const row = document.getElementById(`row-${type}-${assetId}`);
                row.querySelector('.status-text').textContent = newStatus;
                row.querySelector('.keterangan-text').textContent = newKeterangan || '-';
                
                editReportModal.hide();
                Toast.fire({ icon: 'success', title: 'Perubahan berhasil disimpan.' });
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
        });
    });
});
</script>
<?= $this->endSection() ?>