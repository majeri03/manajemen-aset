<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Data Aset
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-4">
            <h4 class="mb-0">Data Aset</h4>
            <p class="text-muted small">Kelola semua aset perusahaan Anda.</p>
        </div>
        <div class="col-md-6 col-lg-8 mt-3 mt-md-0 d-flex justify-content-end align-items-center flex-wrap">
            <div class="input-group search-bar me-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari aset...">
                <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
            </div>
            <button class="btn btn-custom-icon me-2" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                <i class="bi bi-plus-circle me-2"></i> Tambah Aset
            </button>
        </div>
    </div>
</div>

<div class="table-container shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">KODE</th>
                    <th scope="col">KATEGORI</th>
                    <th scope="col">MERK</th>
                    <th scope="col">SERIAL NUMBER</th>
                    <th scope="col">STATUS</th>
                    <th scope="col">LOKASI</th>
                    <th scope="col">AKSI</th>
                </tr>
            </thead>
            <tbody id="asetTableBody">
                <?php if (!empty($asets)): ?>
                    <?php foreach ($asets as $aset): ?>
                        <tr>
                            <td><?= esc($aset['kode']) ?></td>
                            <td><?= esc($aset['kategori']) ?></td>
                            <td><?= esc($aset['merk']) ?></td>
                            <td><?= esc($aset['serial_number']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= esc($aset['status']) ?></span></td>
                            <td><?= esc($aset['lokasi']) ?></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-detail" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailAsetModal"
                                        data-id="<?= $aset['id'] ?>">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data aset.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<?= $this->endSection() ?>