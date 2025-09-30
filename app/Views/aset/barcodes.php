<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Style untuk form filter agar lebih rapi */
    .filter-card .form-label { font-weight: 500; }
    
    /* Style untuk area cetak yang tersembunyi */
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .label {
            page-break-inside: avoid;
            width: 54mm; /* Sesuaikan dengan lebar stiker Anda */
            height: 30mm; /* Sesuaikan dengan tinggi stiker Anda */
            border: 1px dashed #999;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .label img { width: 60px; height: 60px; }
        .label .kode-aset { font-size: 8pt; font-weight: bold; margin: 0; word-break: break-all; }
        .label .detail-aset { font-size: 6pt; margin: 2px 0 0 0; }
    }
</style>

<div class="main-header mb-4">
    <h4 class="mb-0"><?= esc($title) ?></h4>
    <p class="text-muted small">Filter, pilih, dan cetak barcode aset yang Anda butuhkan.</p>
</div>

<div class="card shadow-sm mb-4 filter-card">
    <div class="card-body">
        <form action="<?= base_url('aset/barcodes') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter-lokasi" class="form-label">Lokasi</label>
                <select name="lokasi_id" id="filter-lokasi" class="form-select">
                    <option value="">SEMUA LOKASI</option>
                    <?php foreach ($lokasi_list as $lokasi) : ?>
                        <option value="<?= esc($lokasi['id']) ?>" <?= ($filters['lokasi_id'] ?? '') == $lokasi['id'] ? 'selected' : '' ?>>
                            <?= esc($lokasi['nama_lokasi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter-penanggung-jawab" class="form-label">Penanggung Jawab</label>
                <select name="penanggung_jawab" id="filter-penanggung-jawab" class="form-select">
                    <option value="">SEMUA</option>
                    <?php foreach ($penanggung_jawab_list as $pj) : ?>
                        <option value="<?= esc($pj['penanggung_jawab']) ?>" <?= ($filters['penanggung_jawab'] ?? '') == $pj['penanggung_jawab'] ? 'selected' : '' ?>>
                            <?= esc($pj['penanggung_jawab']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter-merk" class="form-label">Merk</label>
                <select name="merk_id" id="filter-merk" class="form-select">
                    <option value="">SEMUA MERK</option>
                    <?php foreach ($merk_list as $merk) : ?>
                        <option value="<?= esc($merk['id']) ?>" <?= ($filters['merk_id'] ?? '') == $merk['id'] ? 'selected' : '' ?>>
                            <?= esc($merk['nama_merk']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun Beli</label>
                <input type="number" name="tahun" id="tahun" class="form-control" placeholder="Contoh: 2024" value="<?= esc($filters['tahun'] ?? '') ?>">
            </div>
            <div class="col-md-12">
                <label for="keyword" class="form-label">Kata Kunci</label>
                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="CARI KODE, SUB KATEGORI, MERK, TIPE, PENANGGUNG JAWAB..." value="<?= esc($filters['keyword'] ?? '') ?>" oninput="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-md-12 d-flex">
                <button type="submit" class="btn btn-primary flex-grow-1 me-2">FILTER</button>
                <a href="<?= base_url('aset/barcodes') ?>" class="btn btn-secondary flex-grow-1">RESET</a>
            </div>
        </form>
    </div>
</div>

<div class="table-container shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span>Menampilkan <strong><?= count($asets) ?></strong> hasil.</span>
        <button id="printButton" class="btn btn-success"><i class="bi bi-printer-fill me-2"></i>CETAK YANG DIPILIH</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col" style="width: 50px;"><input type="checkbox" id="selectAllCheckbox" class="form-check-input"></th>
                    <th scope="col" style="width: 120px;">BARCODE</th>
                    <th scope="col">KODE ASET</th>
                    <th scope="col">ENTITAS</th>
                    <th scope="col">TAHUN BELI</th>
                    <th scope="col">KATEGORI & SUB</th>
                    <th scope="col">PENANGGUNG JAWAB</th>
                    <th scope="col">LOKASI</th>
                </tr>
            </thead>
            <tbody id="barcodeTableBody">
                <?php if (!empty($asets)): ?>
                    <?php foreach ($asets as $aset): ?>
                        <tr>
                            <td><input type="checkbox" class="form-check-input barcode-checkbox"></td>
                            <td>
                                <img src="<?= base_url($aset['qrcode']) ?>" alt="QR Code" class="img-fluid" style="width: 100px; height: 100px;">
                                <div class="print-data d-none">
                                    <p class="kode-aset"><?= esc($aset['kode'] ?? 'N/A') ?></p>
                                    <p class="detail-aset"><?= esc($aset['nama_sub_kategori'] ?? 'N/A') ?></p> 
                                    <img src="<?= base_url($aset['qrcode']) ?>" alt="QR Code">
                                </div>
                            </td>
                            <td class="fw-bold"><?= esc($aset['kode'] ?? 'N/A') ?></td>
                            <td><?= esc($aset['entitas_pembelian'] ?? 'N/A') ?></td>
                            <td><?= esc($aset['tahun'] ?? 'N/A') ?></td>
                            <td>
                                <div><?= esc($aset['nama_kategori'] ?? 'N/A') ?></div>
                                <small class="text-muted"><?= esc($aset['nama_sub_kategori'] ?? 'N/A') ?></small>
                            </td>
                            <td><?= esc($aset['penanggung_jawab'] ?? 'N/A') ?></td>
                            <td><?= esc($aset['nama_lokasi'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center p-5">
                            <h5 class="text-muted">Data Tidak Ditemukan</h5>
                            <p>Coba ubah atau reset filter Anda.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.barcode-checkbox');
    const printButton = document.getElementById('printButton');

    selectAllCheckbox.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    printButton.addEventListener('click', function() {
        let printArea = document.querySelector('.print-area');
        if (!printArea) {
            printArea = document.createElement('div');
            printArea.classList.add('print-area');
            document.body.appendChild(printArea);
        }
        printArea.innerHTML = '';

        const labelGrid = document.createElement('div');
        labelGrid.classList.add('label-grid');

        let selectedCount = 0;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedCount++;
                const row = checkbox.closest('tr');
                const printDataHTML = row.querySelector('.print-data').innerHTML;
                
                const label = document.createElement('div');
                label.classList.add('label');
                label.innerHTML = printDataHTML;
                labelGrid.appendChild(label);
            }
        });

        if (selectedCount > 0) {
            printArea.appendChild(labelGrid);
            window.print();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Barcode Dipilih',
                text: 'Silakan pilih setidaknya satu barcode untuk dicetak.',
            });
        }
    });
});
</script>
<?= $this->endSection() ?>