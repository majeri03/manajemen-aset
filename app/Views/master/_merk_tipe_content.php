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
                            <h2 class="accordion-header" id="heading-merk-<?= $merk['id'] ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-merk-<?= $merk['id'] ?>" aria-expanded="false" aria-controls="collapse-merk-<?= $merk['id'] ?>">
                                    <?= esc($merk['nama_merk']) ?>
                                    <a href="<?= base_url('master-data/merk/delete/' . $merk['id']) ?>" class="btn btn-sm btn-outline-danger ms-auto" onclick="return confirm('Yakin ingin menghapus merk ini? Semua tipe terkait akan ikut terhapus.')" style="margin-right: 15px;">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </button>
                            </h2>
                            <div id="collapse-merk-<?= $merk['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading-merk-<?= $merk['id'] ?>" data-bs-parent="#merkAccordion">
                                <div class="accordion-body">
                                    <?php
                                        $db = \Config\Database::connect();
                                        $tipes = $db->table('tipe')->where('merk_id', $merk['id'])->get()->getResultArray();
                                    ?>
                                    <?php if (!empty($tipes)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($tipes as $tipe): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?= esc($tipe['nama_tipe']) ?>
                                                    <a href="<?= base_url('master-data/tipe/delete/' . $tipe['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin?')"><i class="bi bi-trash"></i></a>
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

<script>
// Pastikan script ini tidak bentrok dengan script sebelumnya
document.addEventListener('DOMContentLoaded', function() {
    const addTipeBtn = document.getElementById('add-tipe-field');
    if (addTipeBtn) {
        let tipeCount = 1;
        addTipeBtn.addEventListener('click', function() {
            tipeCount++;
            const newField = document.createElement('div');
            newField.classList.add('mb-2', 'input-group');
            newField.innerHTML = `
                <input type="text" class="form-control" name="nama_tipe[]" placeholder="Tipe ${tipeCount}" required oninput="this.value = this.value.toUpperCase()">
                <button class="btn btn-outline-danger remove-field" type="button"><i class="bi bi-trash"></i></button>
            `;
            document.getElementById('tipe-fields').appendChild(newField);
        });

        // Event delegation untuk tombol remove
        document.getElementById('tipe-fields').addEventListener('click', function(e) {
            if (e.target.closest('.remove-field')) {
                e.target.closest('.input-group').remove();
            }
        });
    }
});
</script>