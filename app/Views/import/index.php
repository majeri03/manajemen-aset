<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .table-wrapper { overflow-x: auto; padding-bottom: 15px; }
    .table-wrapper table { width: 100%; min-width: 1800px; }
    .table-wrapper th, .table-wrapper td { white-space: nowrap; vertical-align: middle; }
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
    /* CSS untuk tombol hapus dan container item autocomplete */
    .delete-master-item {
        line-height: 1;
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
    .ui-autocomplete-item-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }
    .ui-autocomplete {
        z-index: 9999 !important; /* Memastikan daftar autocomplete muncul di atas elemen lain */
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
            <form id="upload-form" action="<?= base_url('import/upload') ?>" method="post" enctype="multipart/form-data" class="mt-4">
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
                    <a href="<?= base_url('import/template') ?>" class="btn btn-outline-success ms-auto"><i class="bi bi-download"></i></a>
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
                        <th>Entitas Pembelian</th>
                        <th>Tahun</th>
                        <th>Harga Beli</th>
                        <th>Penanggung Jawab</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($import_data as $index => $row): ?>
                        <?php 
                            $hasError = !empty($row['errors']);
                            // Tambahkan kelas 'table-warning' jika ada error
                            $rowClass = ($row['is_duplicate'] ?? false) ? 'table-danger' : ($hasError ? 'table-warning' : '');
                        ?>
                        <tr data-row-index="<?= $index ?>" class="<?= $rowClass ?>">
                            <td>
                                <?= $index + 1 ?>
                                <?php if ($hasError): ?>
                                    <i class="bi bi-exclamation-triangle-fill text-danger" title="<?= implode(', ', $row['errors']) ?>"></i>
                                <?php endif; ?>
                            </td>

                            <td>
                                <input type="text" class="form-control autocomplete-master" data-type="kategori" value="<?= esc($row['kategori']) ?>" required>
                                <input type="hidden" class="autocomplete-id" name="aset[<?= $index ?>][kategori_id]">
                            </td>
                            <td>
                                <input type="text" class="form-control autocomplete-master" data-type="subkategori" value="<?= esc($row['sub_kategori']) ?>" required>
                                <input type="hidden" class="autocomplete-id" name="aset[<?= $index ?>][sub_kategori_id]">
                            </td>
                            <td>
                                <input type="text" class="form-control autocomplete-master" data-type="merk" value="<?= esc($row['merk']) ?>" required>
                                <input type="hidden" class="autocomplete-id" name="aset[<?= $index ?>][merk_id]">
                            </td>
                            <td>
                                <input type="text" class="form-control autocomplete-master" data-type="tipe" value="<?= esc($row['tipe']) ?>" required>
                                <input type="hidden" class="autocomplete-id" name="aset[<?= $index ?>][tipe_id]">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="aset[<?= $index ?>][serial_number]" value="<?= esc($row['serial_number']) ?>">
                                <?php if ($row['is_duplicate'] ?? false): ?>
                                    <small class="text-danger fw-bold d-block mt-1">Duplikat!</small>
                                <?php endif; ?>
                            </td>
                            <td><input type="text" class="form-control" name="aset[<?= $index ?>][entitas_pembelian]" value="<?= esc($row['entitas_pembelian']) ?>" required></td>
                            <td><input type="number" class="form-control" name="aset[<?= $index ?>][tahun]" value="<?= esc($row['tahun']) ?>" required></td>
                            <td><input type="number" class="form-control" name="aset[<?= $index ?>][harga_beli]" value="<?= esc($row['harga_beli']) ?>"></td>
                            <td><input type="text" class="form-control" name="aset[<?= $index ?>][penanggung_jawab]" value="<?= esc($row['penanggung_jawab']) ?>"></td>
                            <td>
                                <input type="text" class="form-control autocomplete-master" data-type="lokasi" value="<?= esc($row['lokasi']) ?>" required>
                                <input type="hidden" class="autocomplete-id" name="aset[<?= $index ?>][lokasi_id]">
                            </td>
                            <td>
                                <select class="form-select" name="aset[<?= $index ?>][status]">
                                    <option value="Baik Terpakai" <?= $row['status'] == 'Baik Terpakai' ? 'selected' : '' ?>>Baik Terpakai</option>
                                    <option value="Baik Tidak Terpakai" <?= $row['status'] == 'Baik Tidak Terpakai' ? 'selected' : '' ?>>Baik Tidak Terpakai</option>
                                    <option value="Rusak" <?= $row['status'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control" name="aset[<?= $index ?>][keterangan]" value="<?= esc($row['keterangan']) ?>"></td>
                        </tr>
                        <?php if ($hasError): ?>
                            <tr class="table-danger-light">
                                <td colspan="13">
                                    <div class="px-3 py-1 text-danger-emphasis">
                                        <strong><i class="bi bi-exclamation-circle-fill me-2"></i>Kesalahan:</strong>
                                        <ul class="mb-0 ps-4">
                                        <?php foreach($row['errors'] as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
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
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Popup untuk form UPLOAD
    const uploadForm = document.getElementById('upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function() {
            // Cek apakah file sudah dipilih
            const fileInput = document.getElementById('excel_file');
            if (fileInput.files.length > 0) {
                Swal.fire({
                    title: 'Memproses File...',
                    text: 'Mohon tunggu, sistem sedang membaca data Excel Anda.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }

    // 2. Popup untuk form SIMPAN DATA
    const saveForm = document.getElementById('save-form');
    if (saveForm) {
        saveForm.addEventListener('submit', function() {
            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Mohon tunggu, data sedang divalidasi dan disimpan ke database.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});


$(document).ready(function() {
    // Data master dari PHP, diformat untuk autocomplete
    const masterData = {
        kategori: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_kategori']], $kategori)) ?>,
        subkategori: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_sub_kategori'], 'parent_id' => $item['kategori_id']], $subkategori)) ?>,
        merk: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_merk']], $merk)) ?>,
        lokasi: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_lokasi']], $lokasi)) ?>,
        tipe: [] // Data tipe akan diisi via AJAX
    };

    // Objek untuk melacak item yang baru ditambahkan
    let newlyAddedItems = {
        kategori: [], subkategori: [], merk: [], tipe: [], lokasi: []
    };

    // Fungsi inisialisasi autocomplete untuk sebuah baris
    function initializeAutocompleteForRow($row) {
        $row.find('.autocomplete-master').each(function() {
            const $input = $(this);
            const $hiddenInput = $input.next('.autocomplete-id');
            const masterType = $input.data('type');

            // Set nilai awal dari data Excel/session
            setInitialValue($input, $hiddenInput, masterType, $row);

            $input.autocomplete({
                source: (request, response) => getSourceDataForInput($input, request, response),
                minLength: 0,
                select: function(event, ui) {
                    if (ui.item.isNew) {
                        handleNewMasterData($input, ui.item.value);
                    } else {
                        $input.val(ui.item.label);
                        $hiddenInput.val(ui.item.id).trigger('change');
                    }
                    return false;
                },
                change: function(event, ui) {
                    if (!ui.item) {
                        validateInputOnBlur($input, $hiddenInput);
                    }
                }
            }).focus(function() {
                $(this).autocomplete("search", "");
            });

            // Kustomisasi render item untuk menambahkan tombol hapus
            $input.autocomplete("instance")._renderItem = function(ul, item) {
                let itemContent = `<div>${item.label}</div>`;

                if (item.id && newlyAddedItems[masterType]?.includes(item.id)) {
                    itemContent = `
                        <div class="ui-autocomplete-item-container">
                            <span>${item.label}</span>
                            <button type="button" class="btn btn-outline-danger btn-sm delete-master-item"
                                    data-id="${item.id}" data-type="${masterType}" title="Hapus Permanen">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>`;
                }

                return $("<li>").append(itemContent).appendTo(ul);
            };
        });
    }


    function createAndAppendEmptyRow() {
        const tableBody = $('#import-table tbody');
        const newIndex = tableBody.find('tr').length;
        const newRowHtml = `
            <tr data-row-index="${newIndex}">
                <td>${newIndex + 1}</td>
                <td><input type="text" class="form-control autocomplete-master" data-type="kategori" required><input type="hidden" class="autocomplete-id" name="aset[${newIndex}][kategori_id]"></td>
                <td><input type="text" class="form-control autocomplete-master" data-type="subkategori" required><input type="hidden" class="autocomplete-id" name="aset[${newIndex}][sub_kategori_id]"></td>
                <td><input type="text" class="form-control autocomplete-master" data-type="merk" required><input type="hidden" class="autocomplete-id" name="aset[${newIndex}][merk_id]"></td>
                <td><input type="text" class="form-control autocomplete-master" data-type="tipe" required><input type="hidden" class="autocomplete-id" name="aset[${newIndex}][tipe_id]"></td>
                <td><input type="text" class="form-control" name="aset[${newIndex}][serial_number]"></td>
                <td><input type="text" class="form-control" name="aset[${newIndex}][entitas_pembelian]" required></td>
                <td><input type="number" class="form-control" name="aset[${newIndex}][tahun]" required></td>
                <td><input type="number" class="form-control" name="aset[${newIndex}][harga_beli]"></td>
                <td><input type="text" class="form-control" name="aset[${newIndex}][penanggung_jawab]"></td>
                <td><input type="text" class="form-control autocomplete-master" data-type="lokasi" required><input type="hidden" class="autocomplete-id" name="aset[${newIndex}][lokasi_id]"></td>
                <td><select class="form-select" name="aset[${newIndex}][status]"><option value="Baik Terpakai">Baik Terpakai</option><option value="Baik Tidak Terpakai">Baik Tidak Terpakai</option><option value="Rusak">Rusak</option></select></td>
                <td><input type="text" class="form-control" name="aset[${newIndex}][keterangan]"></td>
            </tr>`;
        const $newRow = $(newRowHtml);
        tableBody.append($newRow);
        initializeAutocompleteForRow($newRow);
        attachListenerToLastRow();
    }
    
    // Event listener untuk tombol hapus
    $(document).on('click', '.delete-master-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const button = $(this);
        const id = button.data('id');
        const type = button.data('type');

        Swal.fire({
            title: 'Hapus Item?',
            text: "Item ini akan dihapus permanen dari database. Anda yakin?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('import/delete-master') ?>",
                    method: 'POST',
                    data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>', type: type, id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            masterData[type] = masterData[type].filter(item => item.id != id);
                            newlyAddedItems[type] = newlyAddedItems[type].filter(itemId => itemId != id);
                            $('.ui-autocomplete-input').autocomplete('close');
                            Swal.fire('Terhapus!', response.message, 'success');
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
    
    // --- FUNGSI-FUNGSI BANTUAN ---
    function setInitialValue($input, $hiddenInput, masterType, $row) {
        const initialValue = $input.val();
        if (!initialValue) return;

        let sourceData = [];
        if (masterType === 'subkategori') {
            const kategoriId = $row.find('[data-type="kategori"]').next('.autocomplete-id').val();
            if(kategoriId) sourceData = masterData.subkategori.filter(s => s.parent_id == kategoriId);
        } else if (masterType !== 'tipe') {
            sourceData = masterData[masterType] || [];
        }

        const foundItem = sourceData.find(item => item.label.toUpperCase() === initialValue.toUpperCase());
        if (foundItem) {
            $hiddenInput.val(foundItem.id);
        }
    }

    function getSourceDataForInput($input, request, response) {
        const masterType = $input.data('type');
        const $row = $input.closest('tr');
        let sourceData = [];

        if (masterType === 'subkategori') {
            const kategoriId = $row.find('[data-type="kategori"]').next('.autocomplete-id').val();
            if (kategoriId) sourceData = masterData.subkategori.filter(s => s.parent_id == kategoriId);
        } else if (masterType === 'tipe') {
            const merkId = $row.find('[data-type="merk"]').next('.autocomplete-id').val();
            if (merkId && !isNaN(merkId)) {
                $.getJSON(`<?= base_url('api/tipe/') ?>${merkId}`, data => {
                    const mappedData = data.map(item => ({ id: item.id, label: item.nama_tipe }));
                    masterData.tipe = mappedData; // Cache data tipe
                    filterAndRespond(request.term, mappedData, response, masterType);
                });
                return;
            } 
        } else {
            sourceData = masterData[masterType] || [];
        }
        filterAndRespond(request.term, sourceData, response, masterType);
    }

    function filterAndRespond(term, data, responseCallback, type) {
        const lowerTerm = term.toLowerCase();
        const filtered = data.filter(item => item.label.toLowerCase().includes(lowerTerm));
        const exactMatch = data.some(item => item.label.toLowerCase() === lowerTerm);

        if (term !== '' && !exactMatch) {
            filtered.push({ label: `+ Tambah Baru: "${term}"`, value: term, isNew: true });
        }
        responseCallback(filtered);
    }

    function validateInputOnBlur($input, $hiddenInput) {
        const currentValue = $input.val();
        if (currentValue === '') {
            $hiddenInput.val('').trigger('change');
            return;
        }
        
        const sourceData = getSourceDataForInput($input, { term: '' }, () => {});
        const match = (sourceData || []).find(item => item.label.toLowerCase() === currentValue.toLowerCase());

        if (!match) {
            $input.val('');
            $hiddenInput.val('').trigger('change');
        }
    }
    
    function handleNewMasterData($input, name) {
        const masterType = $input.data('type');
        const $hiddenInput = $input.next('.autocomplete-id');
        const $row = $input.closest('tr');
        let parentId = null;
        if (masterType === 'subkategori') parentId = $row.find('[data-type="kategori"]').next('.autocomplete-id').val();
        if (masterType === 'tipe') parentId = $row.find('[data-type="merk"]').next('.autocomplete-id').val();
        
        $.ajax({
            url: "<?= base_url('import/add-master') ?>",
            method: 'POST',
            data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>', type: masterType, name: name, parent_id: parentId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $input.val(response.text);
                    $hiddenInput.val(response.id).trigger('change');
                    
                    let newItem = { id: response.id, label: response.text };
                    if(parentId) newItem.parent_id = parentId;
                    
                    if(masterData[masterType]) {
                        masterData[masterType].push(newItem);
                        newlyAddedItems[masterType].push(response.id);
                    }
                } else {
                    Swal.fire('Gagal!', response.message || 'Gagal menyimpan data baru.', 'error');
                    $input.val('');
                    $hiddenInput.val('').trigger('change');
                }
            }
        });
    }

    // Inisialisasi awal untuk semua baris yang ada
    $('#import-table tbody tr').each(function() {
        initializeAutocompleteForRow($(this));
    });
    
    // Auto save session data on change
    $('#import-table tbody').on('input paste change', 'input, select', function() {
        const $el = $(this);

        // Abaikan event dari hidden input untuk mencegah pengiriman ganda
        if ($el.is(':hidden')) {
            return;
        }

        const $row = $el.closest('tr');
        const rowIndex = $row.data('row-index');

        // Dapatkan nama field. Untuk autocomplete, acuannya adalah nama dari hidden input.
        const nameAttr = $el.attr('name') || $el.next('.autocomplete-id').attr('name');

        if (typeof rowIndex === 'undefined' || !nameAttr) {
            return; // Hentikan jika tidak ada row index atau nama field
        }

        const fieldName = nameAttr.match(/\[(\w+)\]$/)[1];
        const valueToSend = $el.val();

        // Kirim teks mentah yang diketik/dipilih pengguna ke server
        $.ajax({
            url: "<?= base_url('import/update-session') ?>",
            method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                rowIndex: rowIndex,
                fieldName: fieldName,
                value: valueToSend,
            }
        });
    });
});
// Lokasi: app/Views/import/index.php (di dalam tag <script>)

$(document).ready(function() {
    // --- DATA MASTER DARI PHP ---
    const masterData = {
        kategori: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_kategori']], $kategori)) ?>,
        subkategori: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_sub_kategori'], 'parent_id' => $item['kategori_id']], $subkategori)) ?>,
        merk: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_merk']], $merk)) ?>,
        lokasi: <?= json_encode(array_map(fn($item) => ['id' => $item['id'], 'label' => $item['nama_lokasi']], $lokasi)) ?>,
    };

    // --- FUNGSI UNTUK MENYIMPAN PERUBAHAN KE SESI ---
    function saveChangeToSession(element) {
        const $el = $(element);
        const $row = $el.closest('tr');
        const rowIndex = $row.data('row-index');
        
        const nameAttr = $el.attr('name') || $el.next('.autocomplete-id').attr('name');
        if (typeof rowIndex === 'undefined' || !nameAttr) return;

        const fieldName = nameAttr.match(/\[(\w+)\]$/)[1];
        const valueToSend = $el.val();
        let idToSend = null;

        if ($el.hasClass('autocomplete-master')) {
            idToSend = $el.next('.autocomplete-id').val();
        }

        $.ajax({
            url: "<?= base_url('import/update-session') ?>",
            method: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                rowIndex: rowIndex,
                fieldName: fieldName,
                value: valueToSend,
                id: idToSend
            }
        });
    }

    // --- INISIALISASI AUTOCOMPLETE DAN EVENT ---
    function initializeRow(row) {
        const $row = $(row);
        
        // 1. Inisialisasi Autocomplete (Searchbox)
        $row.find('.autocomplete-master').each(function() {
            const $input = $(this);
            const $hiddenInput = $input.next('.autocomplete-id');
            const masterType = $input.data('type');

            // Set nilai awal dari data yang di-load
            const initialText = $input.val();
            if(initialText) {
                let source = [];
                 // Logika untuk mengisi ID awal berdasarkan teks, dengan mempertimbangkan parent
                if (masterType === 'subkategori') {
                    const kategoriId = $row.find('[data-type="kategori"]').next('.autocomplete-id').val();
                    source = masterData.subkategori.filter(s => s.parent_id == kategoriId);
                } else {
                    source = masterData[masterType] || [];
                }

                const foundItem = source.find(item => item.label.toUpperCase() === initialText.toUpperCase());
                if(foundItem) {
                    $hiddenInput.val(foundItem.id);
                }
            }
            
$input.autocomplete({
                // =================================================================
                // INI BAGIAN UTAMA YANG DIPERBAIKI
                // =================================================================
                source: function(request, response) {
                    let sourceData = [];
                    const term = request.term.toLowerCase();

                    // LOGIKA BARU UNTUK FILTER BERDASARKAN PARENT
                    if (masterType === 'subkategori') {
                        const kategoriId = $row.find('[data-type="kategori"]').next('.autocomplete-id').val();
                        if (kategoriId) {
                            sourceData = masterData.subkategori.filter(s => s.parent_id == kategoriId);
                        }
                    } else if (masterType === 'tipe') {
                        const merkId = $row.find('[data-type="merk"]').next('.autocomplete-id').val();
                        if (merkId) {
                            // Ambil data Tipe dari server berdasarkan Merk ID
                            $.getJSON(`<?= base_url('api/tipe/') ?>${merkId}`, function(data) {
                                const tipes = data.map(item => ({ id: item.id, label: item.nama_tipe }));
                                const filtered = tipes.filter(item => item.label.toLowerCase().includes(term));
                                if (term !== '' && !tipes.some(item => item.label.toLowerCase() === term)) {
                                     filtered.push({ label: `+ Tambah Baru: "${request.term}"`, value: request.term, isNew: true });
                                }
                                response(filtered);
                            });
                            return; // Hentikan eksekusi karena AJAX berjalan asynchronous
                        }
                    } else {
                        sourceData = masterData[masterType] || [];
                    }

                    // Filter data berdasarkan ketikan pengguna
                    const filtered = sourceData.filter(item => item.label.toLowerCase().includes(term));
                    const exactMatch = sourceData.some(item => item.label.toLowerCase() === term);
                    if (term !== '' && !exactMatch) {
                        filtered.push({ label: `+ Tambah Baru: "${request.term}"`, value: request.term, isNew: true });
                    }
                    response(filtered);
                },
 minLength: 0,
                select: function(event, ui) {
                    const $currentInput = $(this);
                    const masterType = $currentInput.data('type');

                    if (ui.item.isNew) {
                        handleNewMasterData($currentInput, ui.item.value);
                    } else {
                        $currentInput.val(ui.item.label);
                        $currentInput.next('.autocomplete-id').val(ui.item.id);
                        
                        // LOGIKA BARU: Jika Kategori atau Merk berubah, kosongkan anaknya
                        if (masterType === 'kategori') {
                            const $subKategoriInput = $row.find('[data-type="subkategori"]');
                            $subKategoriInput.val('');
                            $subKategoriInput.next('.autocomplete-id').val('');
                        } else if (masterType === 'merk') {
                            const $tipeInput = $row.find('[data-type="tipe"]');
                            $tipeInput.val('');
                            $tipeInput.next('.autocomplete-id').val('');
                        }

                        saveChangeToSession(this); 
                    }
                    return false;
                }
            }).focus(function() {
                $(this).autocomplete("search", "");
            });
        });

        // Event Listener untuk menyimpan semua perubahan
        $row.find('input, select').on('input paste change', function() {
             saveChangeToSession(this);
        });
    }

    // --- LOOP UNTUK INISIALISASI SETIAP BARIS ---
    $('#import-table tbody tr').each(function() {
        initializeRow(this);
    });

    // --- (Sisa fungsi seperti handleNewMasterData, dll. bisa diletakkan di sini jika ada) ---
    // Pastikan Anda masih memiliki fungsi handleNewMasterData, dll. jika sebelumnya terpisah
    function handleNewMasterData($input, name) {
        // ... (Fungsi ini dari kode Anda sebelumnya, tidak perlu diubah)
    }
});
</script>
<?= $this->endSection() ?>