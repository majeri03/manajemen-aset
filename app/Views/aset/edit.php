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
            <div class="col-md-6">
                <label for="type" class="form-label">Type</label>
                <input type="text" class="form-control" id="type" name="type" value="<?= esc($aset['type']) ?>" oninput="this.value = this.value.toUpperCase()">
            </div>

            <hr class="my-4">

            <div class="col-md-6">
                <label for="kategori_id" class="form-label">Kategori Barang</label>
                <select class="form-select" id="kategori_id" name="kategori_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?= $kategori['id'] ?>" <?= ($aset['kategori_id'] == $kategori['id']) ? 'selected' : '' ?>>
                            <?= esc($kategori['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="sub_kategori_id" class="form-label">Sub Kategori</label>
                <select class="form-select" id="sub_kategori_id" name="sub_kategori_id" required>
                    <option value="">Pilih Sub Kategori</option>
                    <?php foreach ($subkategori_list as $sub): ?>
                        <option value="<?= $sub['id'] ?>" <?= ($aset['sub_kategori_id'] == $sub['id']) ? 'selected' : '' ?>>
                            <?= esc($sub['nama_sub_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tahun" class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun" name="tahun" value="<?= esc($aset['tahun']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="<?= esc($aset['harga_beli']) ?>">
            </div>
            <div class="col-md-6">
                <label for="entitas_pembelian" class="form-label">Entitas Pembelian</label>
                <input type="text" class="form-control" id="entitas_pembelian" name="entitas_pembelian" value="<?= esc($aset['entitas_pembelian']) ?>">
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

<?= $this->section('script') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data sub kategori yang sudah ada di PHP
        const allSubKategoris = <?= json_encode($subkategori_list) ?>;

        const kategoriSelect = document.getElementById('kategori_id');
        const subKategoriSelect = document.getElementById('sub_kategori_id');
        const currentKategoriId = "<?= esc($aset['kategori_id']) ?>";
        const currentSubKategoriId = "<?= esc($aset['sub_kategori_id']) ?>";

        function populateSubKategori(kategoriId, selectedId) {
            subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
            subKategoriSelect.disabled = true;

            const filteredSubkategoris = allSubKategoris.filter(sub => sub.kategori_id == kategoriId);
            
            if (filteredSubkategoris.length > 0) {
                filteredSubkategoris.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.nama_sub_kategori;
                    if (selectedId && selectedId == sub.id) {
                        option.selected = true;
                    }
                    subKategoriSelect.appendChild(option);
                });
                subKategoriSelect.disabled = false;
            }
        }

        // Jalankan saat halaman pertama kali dimuat
        populateSubKategori(currentKategoriId, currentSubKategoriId);

        // Tambahkan event listener untuk perubahan kategori
        kategoriSelect.addEventListener('change', function() {
            populateSubKategori(this.value);
        });
    });
</script>
<?= $this->endSection() ?>