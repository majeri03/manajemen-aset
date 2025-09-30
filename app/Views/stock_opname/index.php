<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Riwayat Verifikasi (Stock Opname)</h4>
    <p class="text-muted small">Lihat dan saring semua catatan verifikasi aset.</p>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('stockopname') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="kategori_id" class="form-label">Kategori Aset</label>
                <select name="kategori_id" id="kategori_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_list as $kategori) : ?>
                        <option value="<?= esc($kategori['id']) ?>" <?= ($filters['kategori_id'] ?? '') == $kategori['id'] ? 'selected' : '' ?>>
                            <?= esc($kategori['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($filters['start_date'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($filters['end_date'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" name="filter" value="true" class="btn btn-primary w-100">Tampilkan Data</button>
            </div>
        </form>
    </div>
</div>

<div class="table-container shadow-sm ">
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= base_url('stockopname/export?' . http_build_query(array_filter($filters))) ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel-fill me-2"></i>Ekspor ke Excel
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Kode Aset</th>
            <th>Sub Kategori</th>
            <th>Merk</th>
            <th>Tipe</th>
            <th>Lokasi Terakhir</th>
            <th>Diverifikasi Oleh</th>
            <th>Tanggal Verifikasi</th>
            <th>Status Verifikasi</th>
        </tr>
    </thead>
    <tbody>
                <?php if (!empty($history)): ?>
                    <?php foreach ($history as $item): ?>
                        <tr>
                            <td><?= esc($item['kode_aset']) ?></td>
                            <td><?= esc($item['nama_sub_kategori']) ?></td>
                            <td><?= esc($item['nama_merk']) ?></td>
                            <td><?= esc($item['nama_tipe']) ?></td>
                            <td><?= esc($item['nama_lokasi']) ?></td>
                            <td><?= esc($item['full_name']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($item['opname_at'])) ?></td>
                            <td>
                                <?php if ($item['ada_perubahan']): ?>
                                    <span class="badge bg-warning">Ada Usulan Perubahan</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Data Sesuai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <p class="text-muted">
                                <?php if(empty($filters['kategori_id'])): ?>
                                    Silakan pilih kategori aset untuk menampilkan riwayat.
                                <?php else: ?>
                                    Tidak ada data riwayat untuk filter yang dipilih.
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?= $this->endSection() ?>