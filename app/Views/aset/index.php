<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Data Aset
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-4">
            <h4 class="mb-0">Data Aset</h4>
            <p class="text-muted small">Kelola semua aset perusahaan Anda.</p>
        </div>
        <div class="col-md-6 col-lg-8 mt-3 mt-md-0 d-flex justify-content-end align-items-center flex-wrap">
            <div class="input-group search-bar me-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari aset...">
                <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
            </div>
            <button class="btn btn-custom-icon me-2" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                <i class="bi bi-plus-circle me-2"></i> Tambah Aset
            </button>
        </div>
    </div>
</div>


<div class="modal fade" id="detailAsetModal" tabindex="-1" aria-labelledby="detailAsetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailAsetModalLabel">Detail Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Kode:</strong> <span id="detail-kode"></span></p>
                <p><strong>Kategori Barang:</strong> <span id="detail-kategori"></span></p>
                <p><strong>Merk:</strong> <span id="detail-merk"></span></p>
                <p><strong>Serial Number:</strong> <span id="detail-serial_number"></span></p>
                <p><strong>Tahun:</strong> <span id="detail-tahun"></span></p>
                <p><strong>Lokasi:</strong> <span id="detail-lokasi"></span></p>
                <p><strong>Keterangan:</strong> <span id="detail-keterangan"></span></p>
                <hr>
                <p><strong>Terakhir Diperbarui:</strong> <span id="detail-updated_at"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="<?= base_url('aset') ?>" method="get" class="row g-3">
            <div class="col-md-3">
                <label for="filter-kategori" class="form-label">Kategori</label>
                <select name="kategori" id="filter-kategori" class="form-select">
                    <option value="">Semua</option>
                    <?php foreach ($kategori_list as $kategori) : ?>
                        <option value="<?= esc($kategori['kategori']) ?>" <?= ($filters['kategori'] ?? '') == $kategori['kategori'] ? 'selected' : '' ?>>
                            <?= esc($kategori['kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter-status" class="form-label">Status</label>
                <select name="status" id="filter-status" class="form-select">
                    <option value="">Semua</option>
                    <option value="Baik" <?= ($filters['status'] ?? '') == 'Baik' ? 'selected' : '' ?>>Baik</option>
                    <option value="Rusak" <?= ($filters['status'] ?? '') == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                    <option value="Tidak terpakai" <?= ($filters['status'] ?? '') == 'Tidak terpakai' ? 'selected' : '' ?>>Tidak terpakai</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="keyword" class="form-label">Kata Kunci</label>
                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari kode, merk, lokasi..." value="<?= esc($filters['keyword'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="<?= base_url('aset') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="<?= base_url('aset/laporan/export') . '?' . http_build_query($filters ?? []) ?>" class="btn btn-success">
        <i class="bi bi-file-earmark-excel-fill me-2"></i>Ekspor ke Excel
    </a>
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

        <?php if ($conflictingId = session()->getFlashdata('conflicting_asset_id')): ?>
            <button type="button" class="btn btn-dark btn-sm ms-3 view-detail" 
                    data-bs-toggle="modal" 
                    data-bs-target="#detailAsetModal"
                    data-id="<?= $conflictingId ?>">
                Lihat Aset yang Sudah Ada
            </button>
        <?php endif; ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


<div class="table-container shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">KODE</th>
                    <th scope="col">KATEGORI</th>
                    <th scope="col">MERK</th>
                    <th scope="col">SERIAL NUMBER</th>
                    <th scope="col">STATUS</th>
                    <th scope="col">LOKASI</th>
                    <th scope="col">AKSI</th>
                </tr>
            </thead>
            <tbody id="asetTableBody">
                <?php if (!empty($asets)): ?>
                    <?php foreach ($asets as $aset): ?>
                        <tr>
                            <td><?= esc($aset['kode']) ?></td>
                            <td><?= esc($aset['kategori']) ?></td>
                            <td><?= esc($aset['merk']) ?></td>
                            <td><?= esc($aset['serial_number']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= esc($aset['status']) ?></span></td>
                            <td><?= esc($aset['lokasi']) ?></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-detail" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="<?= $aset['id'] ?>">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data aset.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="tambahAsetModal" tabindex="-1" aria-labelledby="tambahAsetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahAsetModalLabel">Tambah Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="<?= base_url('aset') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="aset">
                    <input type="hidden" name="redirect_to" value="aset">
                    <div class="mb-3">
                        <label for="kategori-tambah" class="form-label">Kategori Barang</label>
                        <input type="text" class="form-control" id="kategori-tambah" name="kategori" placeholder="Contoh: PRINTER" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="merk-tambah" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="merk-tambah" name="merk" placeholder="Contoh: EPSON" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="serial_number-tambah" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number-tambah" name="serial_number" placeholder="Contoh: XBN4503766" oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="mb-3">
                        <label for="tahun-tambah" class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun-tambah" name="tahun" placeholder="Contoh: 2025" oninput="generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="status-tambah" class="form-label">Status Aset</label>
                        <select class="form-select" id="status-tambah" name="status" required>
                            <option value="Baik" selected>Baik</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Tidak terpakai">Tidak terpakai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi-tambah" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi-tambah" name="lokasi" placeholder="Contoh: HEAD OFFICE - RG. HCGA" oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan-tambah" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan-tambah" name="keterangan" rows="3" oninput="this.value = this.value.toUpperCase();"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kode-tambah" class="form-label">Kode Aset (Otomatis)</label>
                        <input type="text" class="form-control" id="kode-tambah" name="kode" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Aset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">Aset Berhasil Ditambahkan!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="qrCodePrintArea">
                <p>Berikut adalah detail dan QR Code untuk aset baru:</p>
                <h5 id="qr-kode" class="mt-3"></h5>
                <p id="qr-detail" class="text-muted"></p>
                <div class="my-3">
                    <img id="qr-image" src="" alt="QR Code" class="img-fluid" style="max-width: 250px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printQrCode()">
                    <i class="bi bi-printer-fill me-2"></i>Cetak
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // FUNGSI GLOBAL UNTUK GENERATE KODE ASET
    function generateKodeAset() {
        const kategori = document.getElementById('kategori-tambah').value.toUpperCase().replace(/\s+/g, '').substring(0, 5);
        const tahun = document.getElementById('tahun-tambah').value;
        const merk = document.getElementById('merk-tambah').value.toUpperCase().replace(/\s+/g, '').substring(0, 3);
        
        if (kategori && tahun && merk) {
            document.getElementById('kode-tambah').value = `BTR/${kategori}/${tahun}/${merk}`;
        } else {
            document.getElementById('kode-tambah').value = '';
        }
    }

    // SEMUA EVENT LISTENER DIJALANKAN SETELAH HALAMAN SIAP
    document.addEventListener('DOMContentLoaded', function() {
        // --- FUNGSI PENCARIAN ASET ---
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('asetTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function() {
                const keyword = this.value;
                fetch(`/aset/search?q=${keyword}`)
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = ''; // Kosongkan tabel
                        if (data.length > 0) {
                            data.forEach(aset => {
                                const row = `<tr><td>${aset.kode}</td><td>${aset.kategori}</td><td>${aset.merk}</td><td>${aset.serial_number || '-'}</td><td><span class="badge bg-light text-dark">${aset.status}</span></td><td>${aset.lokasi}</td><td><button type="button" class="btn btn-info btn-sm view-detail" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="${aset.id}"><i class="bi bi-eye-fill"></i></button></td></tr>`;
                                tableBody.innerHTML += row;
                            });
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="7" class="text-center">Aset tidak ditemukan.</td></tr>`;
                        }
                    })
                    .catch(error => console.error('Error searching:', error));
            });
        }

        // --- FUNGSI DETAIL ASET (MODAL) ---
        const detailAsetModal = document.getElementById('detailAsetModal');
        if (detailAsetModal) {
            detailAsetModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const asetId = button.getAttribute('data-id');
                fetch(`/aset/${asetId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Mengisi data teks seperti biasa
                        document.getElementById('detail-kode').textContent = data.kode;
                        document.getElementById('detail-kategori').textContent = data.kategori;
                        document.getElementById('detail-merk').textContent = data.merk;
                        document.getElementById('detail-serial_number').textContent = data.serial_number || '-';
                        document.getElementById('detail-tahun').textContent = data.tahun;
                        document.getElementById('detail-lokasi').textContent = data.lokasi || '-';
                        document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                        // Perbaikan format tanggal agar lebih mudah dibaca
                        document.getElementById('detail-updated_at').textContent = new Date(data.updated_at).toLocaleString('id-ID');
                    })
                    .catch(error => console.error('Error fetching detail:', error));
            });
        }


    });
    // --- LOGIKA UNTUK MENAMPILKAN POPUP QR CODE ---
    <?php if (session()->getFlashdata('new_aset')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const newAset = <?= json_encode(session()->getFlashdata('new_aset')) ?>;
            document.getElementById('qr-kode').textContent = newAset.kode;
            document.getElementById('qr-detail').textContent = `${newAset.kategori} - ${newAset.merk}`;
            document.getElementById('qr-image').src = `<?= base_url() ?>/${newAset.qrcode}`;
            const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            qrModal.show();
        });
    <?php endif; ?>

    // --- FUNGSI UNTUK MENCETAK AREA QR CODE ---
    function printQrCode() {
        const printContent = document.getElementById('qrCodePrintArea').innerHTML;
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload(); 
    }


</script>
<?= $this->endSection() ?>