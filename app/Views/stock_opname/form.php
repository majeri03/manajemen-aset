<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Verifikasi Aset (Stock Opname)</h4>
    <p class="text-muted small">Anda sedang memverifikasi aset dengan kode: <strong><?= esc($aset['kode']) ?></strong></p>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="table-container shadow-sm">
            <form action="<?= base_url('stockopname/process/' . $aset['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <h5>Data Aset Saat Ini</h5>
                        <p class="mb-2"><strong>Kategori:</strong> <?= esc($aset['nama_kategori']) ?></p>
                        <p class="mb-2"><strong>Sub Kategori:</strong> <?= esc($aset['nama_sub_kategori']) ?></p>
                        <p class="mb-2"><strong>Merk:</strong> <?= esc($aset['nama_merk']) ?></p>
                        <p class="mb-2"><strong>Tipe:</strong> <?= esc($aset['nama_tipe']) ?></p>
                        <p class="mb-2"><strong>Serial Number:</strong> <?= esc($aset['serial_number']) ?: '-' ?></p>
                        <p class="mb-2"><strong>Tahun Perolehan:</strong> <?= esc($aset['tahun']) ?></p>
                        <p class="mb-2"><strong>Penanggung Jawab:</strong> <?= esc($aset['penanggung_jawab']) ?: '-' ?></p>
                    </div>

                    <div class="col-md-6">
                        <h5>Verifikasi & Perubahan Data</h5>
                        
                        <div class="mb-3">
                            <label for="lokasi_id" class="form-label">Lokasi</label>
                            <select class="form-select" id="lokasi_id" name="lokasi_id" required>
                                <option value="">Pilih Lokasi</option>
                                <?php foreach ($lokasi_list as $lokasi): ?>
                                    <option value="<?= $lokasi['id'] ?>" <?= ($aset['lokasi_id'] == $lokasi['id']) ? 'selected' : '' ?>>
                                        <?= esc($lokasi['nama_lokasi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status Aset</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Baik Terpakai" <?= $aset['status'] == 'Baik Terpakai' ? 'selected' : '' ?>>Baik (Terpakai)</option>
                                <option value="Baik Tidak Terpakai" <?= $aset['status'] == 'Baik Tidak Terpakai' ? 'selected' : '' ?>>Baik (Tidak Terpakai)</option>
                                <option value="Rusak" <?= $aset['status'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan / Catatan Verifikasi (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="4" oninput="this.value = this.value.toUpperCase()"><?= esc($aset['keterangan']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-check-circle-fill me-2"></i> Kirim Data Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>