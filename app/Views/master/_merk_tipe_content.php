<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Daftar Merk & Tipe</h5>
            
            <form action="<?= base_url('master-data/merk/create') ?>" method="post" class="d-flex gap-2 mb-4">
                <?= csrf_field() ?>
                <input type="text" class="form-control" name="nama_merk" placeholder="Tambah Merk Baru..." required oninput="this.value = this.value.toUpperCase()">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>

            <div class="accordion" id="merkAccordion">
                <?php if (!empty($merks)): ?>
                    <?php foreach ($merks as $merk): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header d-flex align-items-center" id="heading-merk-<?= $merk['id'] ?>">
                                <button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-merk-<?= $merk['id'] ?>" aria-expanded="false" aria-controls="collapse-merk-<?= $merk['id'] ?>">
                                    <?= esc($merk['nama_merk']) ?>
                                </button>
                                
                                <div class="action-buttons p-2">
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            data-id="<?= $merk['id'] ?>" 
                                            data-nama="<?= esc($merk['nama_merk']) ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMerkModal"
                                            title="Edit Merk">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?= base_url('master-data/merk/delete/' . $merk['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Anda yakin ingin menghapus merk ini? Semua tipe yang terkait akan ikut terhapus.')" 
                                       title="Hapus Merk <?= esc($merk['nama_merk']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </h2>
                            <div id="collapse-merk-<?= $merk['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading-merk-<?= $merk['id'] ?>" data-bs-parent="#merkAccordion">
                                <div class="accordion-body">
                                    <?php
                                        $db = \Config\Database::connect();
                                        $tipes = $db->table('tipe')->where('merk_id', $merk['id'])->orderBy('nama_tipe', 'ASC')->get()->getResultArray();
                                    ?>
                                    <?php if (!empty($tipes)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($tipes as $tipe): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?= esc($tipe['nama_tipe']) ?>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-warning me-1"
                                                                data-id="<?= $tipe['id'] ?>"
                                                                data-nama="<?= esc($tipe['nama_tipe']) ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editTipeModal"
                                                                title="Edit Tipe">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <a href="<?= base_url('master-data/tipe/delete/' . $tipe['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tipe ini?')"><i class="bi bi-trash"></i></a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada tipe untuk merk ini.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">Belum ada merk yang ditambahkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Tambah Tipe Baru</h5>
            <form action="<?= base_url('master-data/tipe/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="merk_id" class="form-label">Pilih Merk</label>
                    <select class="form-select" name="merk_id" required>
                        <option value="">-- Pilih Merk --</option>
                        <?php foreach ($merks as $merk): ?>
                            <option value="<?= $merk['id'] ?>"><?= esc($merk['nama_merk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="tipe-fields">
                    <div class="mb-2">
                        <label class="form-label">Nama Tipe</label>
                        <input type="text" class="form-control" name="nama_tipe[]" placeholder="Tipe 1" required oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>

                <button type="button" id="add-tipe-field" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-plus"></i> Tambah Form</button>
                <button type="submit" class="btn btn-primary w-100 mt-3">Simpan Tipe</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editMerkModal" tabindex="-1" aria-labelledby="editMerkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editMerkForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Edit Merk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label for="nama_merk_edit" class="form-label">Nama Merk</label>
                    <input type="text" class="form-control" id="nama_merk_edit" name="nama_merk" oninput="this.value = this.value.toUpperCase()" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTipeModal" tabindex="-1" aria-labelledby="editTipeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editTipeForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Edit Tipe</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label for="nama_tipe_edit" class="form-label">Nama Tipe</label>
                    <input type="text" class="form-control" id="nama_tipe_edit" name="nama_tipe" oninput="this.value = this.value.toUpperCase()" required>
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
    // Script untuk menambah form input tipe (tetap ada)
    const addTipeBtn = document.getElementById('add-tipe-field');
    if (addTipeBtn) {
        let tipeCount = 1;
        addTipeBtn.addEventListener('click', function() {
            tipeCount++;
            const newField = document.createElement('div');
            newField.classList.add('mb-2', 'input-group');
            newField.innerHTML = `<input type="text" class="form-control" name="nama_tipe[]" placeholder="Tipe ${tipeCount}" required oninput="this.value = this.value.toUpperCase()"><button class="btn btn-outline-danger remove-field" type="button"><i class="bi bi-trash"></i></button>`;
            document.getElementById('tipe-fields').appendChild(newField);
        });

        document.getElementById('tipe-fields').addEventListener('click', function(e) {
            if (e.target.closest('.remove-field')) {
                e.target.closest('.input-group').remove();
            }
        });
    }

    // [BARU] Script untuk Modal Edit Merk
    const editMerkModal = document.getElementById('editMerkModal');
    if(editMerkModal) {
        editMerkModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const form = editMerkModal.querySelector('form');
            form.action = `<?= base_url('master-data/merk/update/') ?>${id}`;
            editMerkModal.querySelector('#nama_merk_edit').value = nama;
        });
    }

    // [BARU] Script untuk Modal Edit Tipe
    const editTipeModal = document.getElementById('editTipeModal');
    if(editTipeModal) {
        editTipeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const form = editTipeModal.querySelector('form');
            form.action = `<?= base_url('master-data/tipe/update/') ?>${id}`;
            editTipeModal.querySelector('#nama_tipe_edit').value = nama;
        });
    }
});
</script>