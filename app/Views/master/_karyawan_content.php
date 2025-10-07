<div class="row g-4">
    <div class="col-lg-4">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Tambah Karyawan Baru</h5>
            <form action="<?= base_url('master-data/karyawan/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
                    <input type="text" class="form-control" name="nama_karyawan" placeholder="Masukkan nama..." required>
                </div>
                <div class="mb-3">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" placeholder="Contoh: Staff IT" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Daftar Karyawan</h5>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($karyawan_list)): ?>
                            <?php $no = 1; foreach ($karyawan_list as $karyawan): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($karyawan['nama_karyawan']) ?></td>
                                    <td><?= esc($karyawan['jabatan']) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('master-data/karyawan/delete/' . $karyawan['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus data ini?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">Belum ada data karyawan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>