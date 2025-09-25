<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { height: 38px; padding: 6px 12px; border: 1px solid #ced4da; }
    .select2-container { min-width: 180px; }
    .table-wrapper { overflow-x: auto; padding-bottom: 15px; }
    .table-wrapper table { width: 100%; min-width: 1800px; }
    .table-wrapper th, .table-wrapper td { white-space: nowrap; vertical-align: middle; }
    .select2-container--open { z-index: 9999; }

    /* [BARU] CSS untuk tampilan lebih menarik */
    .upload-box {
        border: 2px dashed #3da2ff;
        border-radius: 16px;
        padding: 2rem;
        background-color: #f0f8ff;
        transition: all 0.3s ease;
    }
    .upload-box:hover {
        background-color: #e6f3ff;
        border-color: #003481;
    }
    .step-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        border-left: 5px solid var(--primary-blue);
    }
    .step-card .step-number {
        background-color: var(--primary-blue);
        color: white;
        font-weight: bold;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="main-header mb-4">
    <h4 class="mb-0">Import Data Aset dari Excel</h4>
    <p class="text-muted small">Unggah, validasi, dan simpan data aset dalam jumlah besar.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!$import_data): ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="upload-box text-center">
            <i class="bi bi-file-earmark-arrow-up-fill" style="font-size: 4rem; color: var(--primary-blue);"></i>
            <h5 class="mt-3">Unggah File Anda Di Sini</h5>
            <p class="text-muted">Gunakan file template untuk memastikan format data sudah benar.</p>
            <form action="<?= base_url('import/upload') ?>" method="post" enctype="multipart/form-data" class="mt-4">
                <?= csrf_field() ?>
                <div class="input-group">
                    <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx" required>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Unggah & Tampilkan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="d-flex flex-column gap-3">
            <div class="step-card">
                <div class="d-flex align-items-center">
                    <div class="step-number me-3">1</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Unduh Template</h6>
                        <p class="mb-0 text-muted small">Mulai dengan mengunduh file template Excel.</p>
                    </div>
                    <a href="<?= base_url('assets/template/template_import.xlsx') ?>" class="btn btn-outline-success ms-auto" download><i class="bi bi-download"></i></a>
                </div>
            </div>
            <div class="step-card">
                <div class="d-flex align-items-center">
                    <div class="step-number me-3">2</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Isi Data</h6>
                        <p class="mb-0 text-muted small">Masukkan semua data aset Anda ke dalam template.</p>
                    </div>
                </div>
            </div>
             <div class="step-card">
                <div class="d-flex align-items-center">
                    <div class="step-number me-3">3</div>
                    <div>
                        <h6 class="mb-0 fw-bold">Unggah & Validasi</h6>
                        <p class="mb-0 text-muted small">Unggah file yang sudah diisi untuk divalidasi sistem.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if ($import_data): ?>
<div class="table-container shadow-sm">
    <form id="save-form" action="<?= base_url('import/save') ?>" method="post">
        <?= csrf_field() ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Data Preview (<?= count($import_data) ?> baris)</h5>
            <div>
                <a href="<?= base_url('import/cancel') ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan impor? Semua data yang belum disimpan akan hilang.')"><i class="bi bi-x-circle me-2"></i>Batal</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Simpan Semua Data</button>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="table table-bordered table-hover" id="import-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kategori</th>
                        <th>Sub Kategori</th>
                        <th>Merk</th>
                        <th>Tipe</th>
                        <th>Serial Number</th>
                        <th>Tahun</th>
                        <th>Harga Beli</th>
                        <th>Entitas Pembelian</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php foreach ($import_data as $index => $row): ?>
                            <?php
                                $rowClass = ($row['is_duplicate'] ?? false) ? 'table-danger' : '';
                            ?>
                            <tr data-row-index="<?= $index ?>" class="<?= $rowClass ?>">
                                <td><?= $index + 1 ?></td>
                                <td><select class="form-select select2-master" name="aset[<?= $index ?>][kategori_id]" data-type="kategori" data-value="<?= esc($row['kategori']) ?>" required></select></td>
                                <td><select class="form-select select2-master" name="aset[<?= $index ?>][sub_kategori_id]" data-type="subkategori" data-value="<?= esc($row['sub_kategori']) ?>" required></select></td>
                                <td><select class="form-select select2-master" name="aset[<?= $index ?>][merk_id]" data-type="merk" data-value="<?= esc($row['merk']) ?>" required></select></td>
                                <td><select class="form-select select2-master" name="aset[<?= $index ?>][tipe_id]" data-type="tipe" data-value="<?= esc($row['tipe']) ?>" required></select></td>
                                <td>
                                    <input type="text" class="form-control" name="aset[<?= $index ?>][serial_number]" value="<?= esc($row['serial_number']) ?>" oninput="this.value = this.value.toUpperCase()">
                                    <?php if ($row['is_duplicate'] ?? false): ?>
                                        <small class="text-danger fw-bold d-block mt-1">Duplikat!</small>
                                    <?php endif; ?>
                                </td>
                                <td><input type="number" class="form-control" name="aset[<?= $index ?>][tahun]" value="<?= esc($row['tahun']) ?>" required></td>
                                <td><input type="number" class="form-control" name="aset[<?= $index ?>][harga_beli]" value="<?= esc($row['harga_beli']) ?>"></td>
                                <td><input type="text" class="form-control" name="aset[<?= $index ?>][entitas_pembelian]" value="<?= esc($row['entitas_pembelian']) ?>" oninput="this.value = this.value.toUpperCase()" required></td>
                                <td><select class="form-select select2-master" name="aset[<?= $index ?>][lokasi_id]" data-type="lokasi" data-value="<?= esc($row['lokasi']) ?>" required></select></td>
                                <td>
                                    <select class="form-select" name="aset[<?= $index ?>][status]">
                                        <option value="Baik Terpakai" <?= $row['status'] == 'Baik Terpakai' ? 'selected' : '' ?>>Baik Terpakai</option>
                                        <option value="Baik Tidak Terpakai" <?= $row['status'] == 'Baik Tidak Terpakai' ? 'selected' : '' ?>>Baik Tidak Terpakai</option>
                                        <option value="Rusak" <?= $row['status'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="aset[<?= $index ?>][keterangan]" value="<?= esc($row['keterangan']) ?>" oninput="this.value = this.value.toUpperCase()"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </form>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Simpan data master dari PHP ke JS
const masterData = {
    kategori: <?= json_encode($kategori) ?>,
    subkategori: <?= json_encode($subkategori) ?>,
    merk: <?= json_encode($merk) ?>,
    lokasi: <?= json_encode($lokasi) ?>,
};

$(document).ready(function() {
    
    function updateSubKategoriOptions($row) {
        const kategoriId = $row.find('select[data-type="kategori"]').val();
        const $subSelect = $row.find('select[data-type="subkategori"]');
        const valueFromSession = $subSelect.data('value');

        $subSelect.empty().append(new Option('', '', false, false));
        if (kategoriId && masterData.subkategori) {
            masterData.subkategori.forEach(item => {
                if (item.kategori_id == kategoriId) {
                    $subSelect.append(new Option(item.nama_sub_kategori, item.id, false, false));
                }
            });
        }
        
        const exists = $subSelect.find('option').filter(function() { return $(this).html().toUpperCase() === valueFromSession.toString().toUpperCase(); }).val();
        if (exists) { $subSelect.val(exists).trigger('change.select2'); } 
        else if (valueFromSession) { $subSelect.append(new Option(valueFromSession, valueFromSession, true, true)).trigger('change.select2'); }
    }

    function updateTipeOptions($row) {
        const merkId = $row.find('select[data-type="merk"]').val();
        const $tipeSelect = $row.find('select[data-type="tipe"]');
        const valueFromSession = $tipeSelect.data('value');

        $tipeSelect.empty().append(new Option('', '', false, false)).prop('disabled', true);

        if (merkId && !isNaN(merkId)) {
            $.getJSON(`<?= base_url('api/tipe/') ?>${merkId}`, function(data) {
                if (data.length > 0) {
                    data.forEach(item => { $tipeSelect.append(new Option(item.nama_tipe, item.id, false, false)); });
                }
                const exists = $tipeSelect.find('option').filter(function() { return $(this).html().toUpperCase() === valueFromSession.toString().toUpperCase(); }).val();
                if (exists) { $tipeSelect.val(exists).trigger('change.select2'); } 
                else if (valueFromSession) { $tipeSelect.append(new Option(valueFromSession, valueFromSession, true, true)).trigger('change.select2'); }
                $tipeSelect.prop('disabled', false);
            });
        } else {
            if (valueFromSession) { $tipeSelect.append(new Option(valueFromSession, valueFromSession, true, true)).trigger('change.select2'); }
        }
    }

    function initializeSelect2ForRow($row) {
        $row.find('.select2-master').each(function() {
            const $select = $(this);
            if ($select.data('select2')) { $select.select2('destroy'); }
            
            const masterType = $select.data('type');
            const valueFromExcel = $select.data('value') || '';

            $select.empty().append(new Option('', '', false, false));
            
            if (masterType !== 'tipe' && masterData[masterType]) {
                masterData[masterType].forEach(item => {
                    const optionName = item[`nama_${masterType}`] || item[`nama_${masterType.replace('kategori', '_kategori')}`];
                    $select.append(new Option(optionName, item.id, false, false));
                });
            }
            
            $select.select2({
                tags: true, width: '100%',
                createTag: function(params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: `(Tambah Baru) ${term}`, newTag: true };
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                if (data.newTag) handleNewMasterData($(this), data);
            }).on('change', function(){
                const $currentRow = $(this).closest('tr');
                if ($(this).data('type') === 'kategori') updateSubKategoriOptions($currentRow);
                if ($(this).data('type') === 'merk') updateTipeOptions($currentRow);
            });

            const exists = $select.find('option').filter(function() {
                return $(this).html().toUpperCase() === valueFromExcel.toString().toUpperCase();
            }).val();

            if (exists) {
                $select.val(exists).trigger('change');
            } else if (valueFromExcel) {
                const newOption = new Option(valueFromExcel, valueFromExcel, true, true);
                $select.append(newOption).trigger('change');
            }
        });
        updateSubKategoriOptions($row);
        updateTipeOptions($row);
    }

    function handleNewMasterData($select, data) {
        const masterType = $select.data('type');
        let parentId = null;
        if (masterType === 'subkategori') parentId = $select.closest('tr').find('select[data-type="kategori"]').val();
        if (masterType === 'tipe') parentId = $select.closest('tr').find('select[data-type="merk"]').val();

        $.ajax({
            url: "<?= base_url('import/add-master') ?>", method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>', type: masterType, name: data.text.replace('(Tambah Baru) ',''), parent_id: parentId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $select.find('[value="' + data.id + '"]').val(response.id).text(response.text.replace('(Tambah Baru) ',''));
                    $select.trigger('change');
                    if (masterData[masterType]) {
                        const optionName = `nama_${masterType}`.replace('kategori', '_kategori');
                        masterData[masterType].push({id: response.id, [optionName]: response.text, 'kategori_id': parentId, 'merk_id': parentId});
                    }
                }
            }
        });
    }

    function createAndAppendEmptyRow() {
        const tableBody = $('#import-table tbody');
        const newIndex = tableBody.find('tr').length;
        const newRowHtml = `<tr data-row-index="${newIndex}"><td>${newIndex + 1}</td><td><select class="form-select select2-master" name="aset[${newIndex}][kategori_id]" data-type="kategori"></select></td><td><select class="form-select select2-master" name="aset[${newIndex}][sub_kategori_id]" data-type="subkategori"></select></td><td><select class="form-select select2-master" name="aset[${newIndex}][merk_id]" data-type="merk"></select></td><td><select class="form-select select2-master" name="aset[${newIndex}][tipe_id]" data-type="tipe"></select></td><td><input type="text" class="form-control" name="aset[${newIndex}][serial_number]"></td><td><input type="number" class="form-control" name="aset[${newIndex}][tahun]"></td><td><input type="number" class="form-control" name="aset[${newIndex}][harga_beli]"></td><td><input type="text" class="form-control" name="aset[${newIndex}][entitas_pembelian]"></td><td><select class="form-select select2-master" name="aset[${newIndex}][lokasi_id]" data-type="lokasi"></select></td><td><select class="form-select" name="aset[${newIndex}][status]"><option value="" selected disabled>Pilih Status</option><option value="Baik Terpakai">Baik Terpakai</option><option value="Baik Tidak Terpakai">Baik Tidak Terpakai</option><option value="Rusak">Rusak</option></select></td><td><input type="text" class="form-control" name="aset[${newIndex}][keterangan]"></td></tr>`;
        const $newRow = $(newRowHtml);
        tableBody.append($newRow);
        initializeSelect2ForRow($newRow);
        attachListenerToLastRow();
    }

    function attachListenerToLastRow() {
        $('#import-table tbody').one('input change', 'tr:last-child input, tr:last-child select', function() {
            if ($(this).val()) {
                createAndAppendEmptyRow();
            }
        });
    }
    
    $('#import-table').on('keydown', 'input, .select2-container', function(e) {
        const $this = $(this).is('input') ? $(this) : $(this).prev('select');
        const $cell = $this.closest('td'); const $row = $this.closest('tr'); let $next;
        switch (e.key) {
            case 'ArrowUp': $next = $row.prev().find('td:eq(' + $cell.index() + ')').find('input, select'); break;
            case 'ArrowDown': $next = $row.next().find('td:eq(' + $cell.index() + ')').find('input, select'); break;
            case 'ArrowLeft': $next = $cell.prev().find('input, select'); break;
            case 'ArrowRight': $next = $cell.next().find('input, select'); break;
            default: return;
        }
        if ($next && $next.length) {
            e.preventDefault();
            if ($next.hasClass('select2-master')) { $next.select2('open'); } else { $next.focus(); }
        }
    });

    $('tbody tr').each(function() { initializeSelect2ForRow($(this)); });
    
    <?php if ($import_data): ?>
        createAndAppendEmptyRow();
    <?php endif; ?>

    $('#import-table tbody').on('change', 'input, select', function() {
        const $el = $(this); const $row = $el.closest('tr');
        const rowIndex = $row.data('row-index'); const nameAttr = $el.attr('name');
        if (typeof rowIndex === 'undefined') return;
        const fieldName = nameAttr.match(/\[(\w+)\]$/)[1]; 
        
        let valueToSend = $el.val();
        // Untuk dropdown, kirim Teks-nya, bukan ID-nya
        if ($el.is('select')) {
             valueToSend = $el.find('option:selected').text();
        }

        $.ajax({
            url: "<?= base_url('import/update-session') ?>", method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>', rowIndex: rowIndex, fieldName: fieldName, value: valueToSend
            },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') { console.log(`Auto-saved`); }
            }
        });
    });

    $('#save-form').on('submit', function(e) {
        const $lastRow = $('#import-table tbody tr:last');
        let isLastRowEmpty = true;
        $lastRow.find('input, select').each(function() {
            if ($(this).val() && $(this).val().trim() !== '') { isLastRowEmpty = false; return false; }
        });
        if (isLastRowEmpty) { $lastRow.remove(); }
    });
});
</script>
<?= $this->endSection() ?>