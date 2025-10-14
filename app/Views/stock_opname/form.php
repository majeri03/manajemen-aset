<!-- <?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <a href="<?= base_url('dashboard') ?>" class="btn btn-light me-3" title="Kembali ke Dashboard"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="mb-0">Verifikasi & Ajukan Perubahan</h4>
        <p class="text-muted small">Aset: <strong><?= esc($aset['kode']) ?></strong></p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">

        <?php if (session()->get('info')): ?>
            <div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i><?= session()->get('info') ?></div>
        <?php endif; ?>
        <?php if (session()->get('success')): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= session()->get('success') ?></div>
        <?php endif; ?>

        <div class="table-container shadow-sm">
            <?php if ($aset['status_verifikasi'] === 'Sudah Dicek'): ?>
                <div class="alert alert-info mb-4">
                    <strong>Informasi:</strong> Aset ini sudah diverifikasi dalam siklus ini. Anda hanya dapat mengirim jika ada perubahan data.
                </div>
            <?php endif; ?>

            <form action="<?= base_url('stockopname/process/' . $aset['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5>Data Aset Saat Ini</h5>
                        <p class="mb-2"><strong>Sub Kategori:</strong> <?= esc($aset['nama_sub_kategori']) ?></p>
                        <p class="mb-2"><strong>Merk:</strong> <?= esc($aset['nama_merk']) ?></p>
                        <p class="mb-2"><strong>Tipe:</strong> <?= esc($aset['nama_tipe']) ?: '-' ?></p>
                        <p class="mb-2"><strong>Serial Number:</strong> <?= esc($aset['serial_number']) ?: '-' ?></p>
                        <p class="mb-2"><strong>Tahun:</strong> <?= esc($aset['tahun']) ?></p>
                        <p class="mb-2"><strong>Entitas:</strong> <?= esc($aset['entitas_pembelian']) ?></p>
                        <p class="mb-2"><strong>Penanggung Jawab:</strong> <?= esc($aset['penanggung_jawab']) ?: '-' ?></p>
                        <hr>
                        <p class="mb-2"><strong>Lokasi Terakhir:</strong> <?= esc($aset['nama_lokasi']) ?></p>
                        <p class="mb-2"><strong>Status Terakhir:</strong> <?= esc($aset['status']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Verifikasi & Usulan Perubahan</h5>
                        <div class="mb-3">
                            <label for="lokasi_id" class="form-label">Lokasi Seharusnya</label>
                            <select class="form-select" id="lokasi_id" name="lokasi_id" required>
                                <?php foreach ($lokasi_list as $lokasi): ?>
                                    <option value="<?= $lokasi['id'] ?>" <?= ($aset['lokasi_id'] == $lokasi['id']) ? 'selected' : '' ?>>
                                        <?= esc($lokasi['nama_lokasi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status/Kondisi Aset</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Baik Terpakai" <?= $aset['status'] == 'Baik Terpakai' ? 'selected' : '' ?>>Baik (Terpakai)</option>
                                <option value="Baik Tidak Terpakai" <?= $aset['status'] == 'Baik Tidak Terpakai' ? 'selected' : '' ?>>Baik (Tidak Terpakai)</option>
                                <option value="Rusak" <?= $aset['status'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                                <option value="Perbaikan" <?= $aset['status'] == 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Catatan / Keterangan Baru</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" oninput="this.value = this.value.toUpperCase()"><?= esc($aset['keterangan']) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-check-circle-fill me-2"></i> Kirim Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?> -->