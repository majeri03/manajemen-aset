<div class="row g-4">
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
                                        <button type="button" class="btn btn-sm btn-warning me-2" 
                                                data-id="<?= $lokasi['id'] ?>" 
                                                data-nama="<?= esc($lokasi['nama_lokasi']) ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editLokasiModal" 
                                                title="Edit Lokasi">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
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

<div class="modal fade" id="editLokasiModal" tabindex="-1" aria-labelledby="editLokasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLokasiModalLabel">Edit Nama Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLokasiForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_lokasi_edit" class="form-label">Nama Lokasi Baru</label>
                        <input type="text" class="form-control" id="nama_lokasi_edit" name="nama_lokasi" oninput="this.value = this.value.toUpperCase()" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editLokasiModal = document.getElementById('editLokasiModal');
    if (editLokasiModal) {
        editLokasiModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const lokasiId = button.getAttribute('data-id');
            const namaLokasi = button.getAttribute('data-nama');

            const form = editLokasiModal.querySelector('#editLokasiForm');
            const inputNama = editLokasiModal.querySelector('#nama_lokasi_edit');

            // Mengatur action form secara dinamis
            form.action = `<?= base_url('master-data/lokasi/update/') ?>${lokasiId}`;
            
            // Mengisi nilai awal pada input field
            inputNama.value = namaLokasi;
        });
    }
});
</script>