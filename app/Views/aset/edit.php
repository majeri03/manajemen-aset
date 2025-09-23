<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Edit Aset
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Edit Aset: <?= esc($aset['kode']) ?></h4>
    <p class="text-muted small">Hanya field yang diizinkan yang dapat diubah.</p>
</div>

<div class="table-container shadow-sm">
    <form action="<?= base_url('aset/' . $aset['id']) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="row g-3">
            <div class="col-md-6">
                <label for="kode" class="form-label">Kode Aset</label>
                <input type="text" class="form-control" id="kode" value="<?= esc($aset['kode']) ?>" readonly style="background-color: #e9ecef;">
            </div>
            <div class="col-md-6">
                <label for="merk" class="form-label">Merk</label>
                <input type="text" class="form-control" id="merk" value="<?= esc($aset['merk']) ?>" readonly style="background-color: #e9ecef;">
            </div>
             <div class="col-md-6">
                <label for="serial_number" class="form-label">Serial Number</label>
                <input type="text" class="form-control" id="serial_number" value="<?= esc($aset['serial_number']) ?>" readonly style="background-color: #e9ecef;">
            </div>

            <hr class="my-4">

            <div class="col-md-6">
                <label for="kategori" class="form-label">Kategori Barang</label>
                <input type="text" class="form-control" id="kategori" name="kategori" value="<?= esc($aset['kategori']) ?>" oninput="this.value = this.value.toUpperCase()" required>
            </div>
            <div class="col-md-6">
                <label for="tahun" class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun" name="tahun" value="<?= esc($aset['tahun']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="lokasi" class="form-label">Lokasi</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?= esc($aset['lokasi']) ?>" oninput="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status Aset</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Baik" <?= $aset['status'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                    <option value="Rusak" <?= $aset['status'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                    <option value="Tidak terpakai" <?= $aset['status'] == 'Tidak terpakai' ? 'selected' : '' ?>>Tidak terpakai</option>
                </select>
            </div>
            <div class="col-12">
                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" oninput="this.value = this.value.toUpperCase()"><?= esc($aset['keterangan']) ?></textarea>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <a href="<?= base_url('aset') ?>" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>