<div class="row g-4">
    <!-- Kolom Kiri: Form Tambah Lokasi -->
    <div class="col-lg-4">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Tambah Lokasi Baru</h5>
            <form action="<?= base_url('master-data/lokasi/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                    <input type="text" class="form-control <?= (session('errors_lokasi.nama_lokasi')) ? 'is-invalid' : '' ?>" id="nama_lokasi" name="nama_lokasi" placeholder="Contoh: GUDANG PUSAT" oninput="this.value = this.value.toUpperCase()" required>
                    <?php if (session('errors_lokasi.nama_lokasi')): ?>
                        <div class="invalid-feedback">
                            <?= session('errors_lokasi.nama_lokasi') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Lokasi</button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Lokasi -->
    <div class="col-lg-8">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Daftar Lokasi Tersedia</h5>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lokasi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lokasis)): ?>
                            <?php $no = 1; foreach ($lokasis as $lokasi): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($lokasi['nama_lokasi']) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('master-data/lokasi/delete/' . $lokasi['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?')" title="Hapus Lokasi">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data lokasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

