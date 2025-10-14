<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    :root {
        --primary-color: #003481;
        --secondary-color: #f0f8ff;
        --text-dark: #343a40;
        --text-muted-light: #8a95a5;
    }
    .info-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }
    .info-header {
        background-color: var(--primary-color);
        color: white;
        padding: 1.5rem;
    }
    .info-body {
        padding: 2rem;
    }
    .info-section h5 {
        color: var(--primary-color);
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.25rem;
    }
    .info-item .icon {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-right: 15px;
        margin-top: 2px;
        width: 20px;
    }
    .info-item .label {
        font-weight: 500;
        color: var(--text-muted-light);
        margin-bottom: 0.25rem;
    }
    .info-item .value {
        font-weight: 600;
        color: var(--text-dark);
        word-break: break-word;
    }
    .status-badge {
        font-size: 1rem;
        padding: 0.5em 0.9em;
    }
</style>

<div class="container my-5">
    <div class="card info-card">
        <div class="info-header">
            <h3 class="mb-0 d-flex align-items-center"><i class="bi bi-archive-fill me-3"></i> Informasi Detail Aset</h3>
        </div>
        <div class="info-body">
        
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="info-section">
                        <h5>Spesifikasi Aset</h5>
                        <div class="row">
                            <div class="col-md-6 info-item">
                                <i class="bi bi-upc-scan icon"></i>
                                <div><div class="label">Kode Aset</div><div class="value"><?= esc($aset['kode']) ?></div></div>
                            </div>
                            <div class="col-md-6 info-item">
                                <i class="bi bi-grid-1x2-fill icon"></i>
                                <div><div class="label">Kategori</div><div class="value"><?= esc($aset['nama_kategori']) ?></div></div>
                            </div>
                            <div class="col-md-6 info-item">
                                <i class="bi bi-tags-fill icon"></i>
                                <div><div class="label">Sub Kategori</div><div class="value"><?= esc($aset['nama_sub_kategori']) ?></div></div>
                            </div>
                            <div class="col-md-6 info-item">
                                <i class="bi bi-bookmark-star-fill icon"></i>
                                <div><div class="label">Merk</div><div class="value"><?= esc($aset['nama_merk']) ?></div></div>
                            </div>
                            <div class="col-md-6 info-item">
                                <i class="bi bi-type icon"></i>
                                <div><div class="label">Tipe</div><div class="value"><?= esc($aset['nama_tipe'] ?? '-') ?></div></div>
                            </div>
                             <div class="col-md-6 info-item">
                                <i class="bi bi-fingerprint icon"></i>
                                <div><div class="label">Serial Number</div><div class="value"><?= esc($aset['serial_number'] ?? '-') ?></div></div>
                            </div>
                             <div class="col-md-6 info-item">
                                <i class="bi bi-calendar-check-fill icon"></i>
                                <div><div class="label">Tahun Pembelian</div><div class="value"><?= esc($aset['tahun_beli']) ?></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="info-section mt-4">
                        <h5>Dokumentasi</h5>
                        <?php if (!empty($dokumen)): ?>
                            <div class="list-group">
                                <?php foreach ($dokumen as $doc): ?>
                                    <a href="<?= base_url('uploads/aset_bukti/' . $doc['file_path']) ?>" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="bi <?= str_contains($doc['tipe_file'], 'pdf') ? 'bi-file-earmark-pdf-fill text-danger' : 'bi-image text-success' ?> me-3 fs-4"></i> 
                                        <?= esc($doc['nama_dokumen']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Tidak ada dokumentasi yang dilampirkan.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="info-section">
                        <h5>Status & Lokasi</h5>
                         <div class="info-item">
                            <i class="bi bi-person-check-fill icon"></i>
                            <div><div class="label">Penanggung Jawab</div><div class="value"><?= esc($aset['nama_karyawan'] ?? 'Tidak Ada') ?></div></div>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-geo-alt-fill icon"></i>
                            <div><div class="label">Lokasi Aset</div><div class="value"><?= esc($aset['nama_lokasi']) ?></div></div>
                        </div>
                        <div class="info-item">
                             <i class="bi bi-clipboard2-check-fill icon"></i>
                             <div>
                                <div class="label">Status Aset</div>
                                <div class="value"><span class="badge rounded-pill status-badge text-bg-success"><?= esc($aset['status']) ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>