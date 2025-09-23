<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Ajukan Perubahan untuk Aset: <?= esc($aset['kode']) ?></h4>
    <p class="text-muted small">Data yang Anda ajukan akan divalidasi oleh Admin.</p>
</div>

<div class="table-container shadow-sm">
    <div class="row">
        <div class="col-lg-6">
            <h5>Data Saat Ini</h5>
            <p><strong>Kategori:</strong> <?= esc($aset['kategori']) ?></p>
            <p><strong>Merk:</strong> <?= esc($aset['merk']) ?></p>
            <p><strong>Lokasi:</strong> <?= esc($aset['lokasi']) ?></p>
            <p><strong>Status:</strong> <?= esc($aset['status']) ?></p>
            <p><strong>Keterangan:</strong> <?= esc($aset['keterangan']) ?></p>
        </div>
        <div class="col-lg-6">
            <h5>Ajukan Perubahan (Isi yang perlu diubah)</h5>
            <form action="<?= base_url('requests') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="aset_id" value="<?= esc($aset['id']) ?>">

                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi Baru</label>
                    <input type="text" class="form-control" id="lokasi" name="proposed_data[lokasi]" placeholder="Kosongkan jika tidak berubah" oninput="this.value = this.value.toUpperCase();">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status Baru</label>
                    <select class="form-select" id="status" name="proposed_data[status]">
                        <option value="">-- Pilih Jika Berubah --</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Tidak terpakai">Tidak terpakai</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan Baru</label>
                    <textarea class="form-control" id="keterangan" name="proposed_data[keterangan]" rows="3" placeholder="Kosongkan jika tidak berubah" oninput="this.value = this.value.toUpperCase();"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>