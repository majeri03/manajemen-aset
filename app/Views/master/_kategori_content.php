<div class="row g-4">
    <div class="col-lg-6">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Daftar Kategori & Sub-Kategori</h5>
            
            <form action="<?= base_url('master-data/kategori/create') ?>" method="post" class="d-flex gap-2 mb-4">
                <?= csrf_field() ?>
                <input type="text" class="form-control" name="nama_kategori" placeholder="Tambah Kategori Utama Baru..." required oninput="this.value = this.value.toUpperCase()">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>

            <div class="accordion" id="kategoriAccordion">
                <?php if (!empty($kategoris)): ?>
                    <?php foreach ($kategoris as $kategori): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header d-flex align-items-center" id="heading-<?= $kategori['id'] ?>">
                                <button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $kategori['id'] ?>" aria-expanded="false" aria-controls="collapse-<?= $kategori['id'] ?>">
                                    <?= esc($kategori['nama_kategori']) ?>
                                </button>
                                
                                <div class="action-buttons p-2">
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            data-id="<?= $kategori['id'] ?>" 
                                            data-nama="<?= esc($kategori['nama_kategori']) ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editKategoriModal"
                                            title="Edit Kategori">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="<?= base_url('master-data/kategori/delete/' . $kategori['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Anda yakin ingin menghapus kategori ini? Semua sub-kategori yang terkait akan ikut terhapus.')"
                                       title="Hapus Kategori <?= esc($kategori['nama_kategori']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </h2>
                            <div id="collapse-<?= $kategori['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $kategori['id'] ?>" data-bs-parent="#kategoriAccordion">
                                <div class="accordion-body">
                                    <?php
                                        $db = \Config\Database::connect();
                                        $subKategoris = $db->table('sub_kategori')->where('kategori_id', $kategori['id'])->orderBy('nama_sub_kategori', 'ASC')->get()->getResultArray();
                                    ?>
                                    <?php if (!empty($subKategoris)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($subKategoris as $sub): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?= esc($sub['nama_sub_kategori']) ?>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-warning me-1"
                                                                data-id="<?= $sub['id'] ?>"
                                                                data-nama="<?= esc($sub['nama_sub_kategori']) ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editSubKategoriModal"
                                                                title="Edit Sub-Kategori">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <a href="<?= base_url('master-data/subkategori/delete/' . $sub['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')"><i class="bi bi-trash"></i></a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada sub-kategori.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">Belum ada kategori yang ditambahkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="table-container shadow-sm h-100">
            <h5 class="mb-3">Tambah Sub-Kategori</h5>
            <form action="<?= base_url('master-data/subkategori/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="kategori_id" class="form-label">Pilih Kategori Utama</label>
                    <select class="form-select" name="kategori_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoris as $kategori): ?>
                            <option value="<?= $kategori['id'] ?>"><?= esc($kategori['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="sub-kategori-fields">
                    <div class="mb-2">
                        <label class="form-label">Nama Sub-Kategori</label>
                        <input type="text" class="form-control" name="nama_sub_kategori[]" placeholder="Sub-kategori 1" required oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>

                <button type="button" id="add-sub-kategori" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-plus"></i> Tambah Form</button>
                <button type="submit" class="btn btn-primary w-100 mt-3">Simpan Sub-Kategori</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editKategoriForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Edit Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label for="nama_kategori_edit" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori_edit" name="nama_kategori" oninput="this.value = this.value.toUpperCase()" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editSubKategoriModal" tabindex="-1" aria-labelledby="editSubKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editSubKategoriForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Edit Sub-Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label for="nama_sub_kategori_edit" class="form-label">Nama Sub-Kategori</label>
                    <input type="text" class="form-control" id="nama_sub_kategori_edit" name="nama_sub_kategori" oninput="this.value = this.value.toUpperCase()" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script untuk menambah form input sub-kategori secara dinamis
    const addSubKategoriBtn = document.getElementById('add-sub-kategori');
    if (addSubKategoriBtn) {
        let count = 1;
        addSubKategoriBtn.addEventListener('click', function() {
            count++;
            const newField = document.createElement('div');
            newField.classList.add('mb-2', 'input-group');
            newField.innerHTML = `<input type="text" class="form-control" name="nama_sub_kategori[]" placeholder="Sub-kategori ${count}" required oninput="this.value = this.value.toUpperCase()"><button class="btn btn-outline-danger remove-field" type="button"><i class="bi bi-trash"></i></button>`;
            document.getElementById('sub-kategori-fields').appendChild(newField);
        });

        document.getElementById('sub-kategori-fields').addEventListener('click', function(e) {
            if (e.target.closest('.remove-field')) {
                e.target.closest('.input-group').remove();
            }
        });
    }

    // JS untuk Modal Edit Kategori
    const editKategoriModal = document.getElementById('editKategoriModal');
    if(editKategoriModal) {
        editKategoriModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const form = editKategoriModal.querySelector('form');
            form.action = `<?= base_url('master-data/kategori/update/') ?>${id}`;
            editKategoriModal.querySelector('#nama_kategori_edit').value = nama;
        });
    }

    // JS untuk Modal Edit Sub-Kategori
    const editSubKategoriModal = document.getElementById('editSubKategoriModal');
    if(editSubKategoriModal) {
        editSubKategoriModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const form = editSubKategoriModal.querySelector('form');
            form.action = `<?= base_url('master-data/subkategori/update/') ?>${id}`;
            editSubKategoriModal.querySelector('#nama_sub_kategori_edit').value = nama;
        });
    }
});
</script>