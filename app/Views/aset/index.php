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
            <div class="col-md-6 col-lg-8 mt-3 mt-md-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group search-bar flex-grow-1">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari aset...">
                        <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
                    </div>
                    <?php if (session()->get('role') === 'admin'): ?>
                    <button class="btn btn-custom-icon flex-shrink-0" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Aset
                    </button>
                    <?php endif; ?>
                </div>
            </div>
    </div>
</div>


<div class="modal fade" id="detailAsetModal" tabindex="-1" aria-labelledby="detailAsetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailAsetModalLabel">Detail Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Kode:</strong> <span id="detail-kode"></span></p>
                        <p><strong>Kategori Barang:</strong> <span id="detail-kategori"></span></p>
                        <p><strong>Sub Kategori:</strong> <span id="detail-sub-kategori"></span></p>
                        <p><strong>Merk:</strong> <span id="detail-merk"></span></p>
                        <p><strong>Type:</strong> <span id="detail-type"></span></p>
                        <p><strong>Serial Number:</strong> <span id="detail-serial_number"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>tahun Beli:</strong> <span id="detail-tahun_beli"></span></p>
                        <p><strong>Harga Beli:</strong> <span id="detail-harga_beli"></span></p>
                        <p><strong>Entitas Pembelian:</strong> <span id="detail-entitas_pembelian"></span></p>
                        <p><strong>User Pengguna:</strong> <span id="detail-user_pengguna"></span></p>
                        <p><strong>Lokasi:</strong> <span id="detail-lokasi"></span></p>
                        <p><strong>Status:</strong> <span id="detail-status"></span></p>
                    </div>
                </div>
                <p><strong>Keterangan:</strong> <span id="detail-keterangan"></span></p>
                <hr>
                <h6 class="mt-4">Dok. Aset</h6>
                <div class="row g-2" id="detail-dokumentasi">
                    </div>
                <hr>
                <ul class="nav nav-tabs" id="historyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="verifikasi-tab" data-bs-toggle="tab" data-bs-target="#verifikasi-content" type="button" role="tab">Riwayat Verifikasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="perubahan-tab" data-bs-toggle="tab" data-bs-target="#perubahan-content" type="button" role="tab">Riwayat Perubahan Data</button>
                    </li>
                </ul>
                <div class="tab-content" id="historyTabsContent">
                    <div class="tab-pane fade show active" id="verifikasi-content" role="tabpanel">
                        <ul class="list-group mt-3" id="timeline-verifikasi-list">
                            </ul>
                    </div>
                    <div class="tab-pane fade" id="perubahan-content" role="tabpanel">
                         <ul class="list-group mt-3" id="timeline-perubahan-list">
                            </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm mb-4">
    <div class="card-body">
<form action="<?= base_url('aset') ?>" method="get" class="row g-3 align-items-center">
    <div class="col-lg-3 col-md-6">
        <label for="filter-kategori" class="form-label">Kategori</label>
        <select name="kategori_id" id="filter-kategori" class="form-select">
            <option value="">Semua</option>
            <?php foreach ($kategori_list as $kategori) : ?>
                <option value="<?= esc($kategori['id']) ?>" <?= ($filters['kategori_id'] ?? '') == $kategori['id'] ? 'selected' : '' ?>>
                    <?= esc($kategori['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-lg-3 col-md-6">
        <label for="filter-status" class="form-label">Status</label>
        <select name="status" id="filter-status" class="form-select">
            <option value="">Semua</option>
            <option value="Baik Terpakai" <?= ($filters['status'] ?? '') == 'Baik Terpakai' ? 'selected' : '' ?>>Baik (Terpakai)</option>
            <option value="Baik Tidak Terpakai" <?= ($filters['status'] ?? '') == 'Baik Tidak Terpakai' ? 'selected' : '' ?>>Baik (Tidak Terpakai)</option>
            <option value="Rusak" <?= ($filters['status'] ?? '') == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
    
        </select>
    </div>
    <div class="col-lg-6 col-md-12">
        <label for="keyword" class="form-label">Kata Kunci</label>
        <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari kode, merk, lokasi..." value="<?= esc($filters['keyword'] ?? '') ?>">
    </div>
    <div class="col-lg-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary me-2" style="width: 150px;">Filter</button>
        <a href="<?= base_url('aset') ?>" class="btn btn-secondary" style="width: 150px;">Reset</a>
    </div>
</form>
    </div>
</div>
<div class="d-flex justify-content-end mb-3">
    <a href="<?= base_url('aset/barcodes') ?>" class="btn btn-info me-2">
        <i class="bi bi-upc-scan me-2"></i>Lihat Semua Barcode
    </a>
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
                    <th scope="col">SUB KATEGORI</th>
                    <th scope="col">MERK</th>
                    <th scope="col">SERIAL NUMBER</th>
                    <th scope="col">USER PENGGUNA</th>
                    <th scope="col">LOKASI</th>
                    <th scope="col">STATUS</th>
                    <th scope="col">AKSI</th>
                </tr>
            </thead>
        <tbody id="asetTableBody">
                <?php if (!empty($asets)): ?>
                    <?php foreach ($asets as $aset): ?>
                        <tr>
                            <td><?= esc($aset['kode']) ?></td>
                            <td><?= esc($aset['nama_sub_kategori']) ?></td>
                            <td><?= esc($aset['nama_merk']) ?></td>
                            <td><?= esc($aset['serial_number']) ?></td>
                            <td><?= esc($aset['user_pengguna']) ?></td>
                            <td><?= esc($aset['nama_lokasi']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= esc($aset['status']) ?></span></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-detail" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="<?= $aset['id'] ?>" title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <?php if (session()->get('role') === 'admin'): ?>
                                <a href="<?= base_url('aset/' . $aset['id'] . '/edit') ?>" class="btn btn-warning btn-sm" title="Edit Aset">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="confirmDelete(this)" data-id="<?= $aset['id'] ?>" data-kode="<?= esc($aset['kode']) ?>" class="btn btn-danger btn-sm" title="Hapus Aset">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data aset.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <form action="" method="post" id="deleteForm">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
        </form>
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
                <p><strong>Sub Kategori:</strong> <span id="detail-sub-kategori"></span></p>
                <p><strong>Merk:</strong> <span id="detail-merk"></span></p>
                <p><strong>Type:</strong> <span id="detail-type"></span></p>
                <p><strong>Serial Number:</strong> <span id="detail-serial_number"></span></p>
                <p><strong>tahun_beli:</strong> <span id="detail-tahun_beli"></span></p>
                <p><strong>Harga Beli:</strong> <span id="detail-harga_beli"></span></p>
                <p><strong>Entitas Pembelian:</strong> <span id="detail-entitas_pembelian"></span></p>
                <p><strong>User Pengguna:</strong> <span id="detail-user_pengguna"></span></p>
                <p><strong>Lokasi:</strong> <span id="detail-lokasi"></span></p>
                <p><strong>Status:</strong> <span id="detail-status"></span></p>
                <p><strong>Keterangan:</strong> <span id="detail-keterangan"></span></p>
                <hr>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="tambahAsetModal" tabindex="-1" aria-labelledby="tambahAsetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahAsetModalLabel">Tambah Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('aset') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_to" value="dashboard">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kategori_id-tambah" class="form-label">Kategori Barang</label>
                            <select class="form-select" id="kategori_id-tambah" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategori_list as $kategori): ?>
                                    <option value="<?= $kategori['id'] ?>"><?= $kategori['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sub_kategori_id-tambah" class="form-label">Sub Kategori</label>
                            <select class="form-select" id="sub_kategori_id-tambah" name="sub_kategori_id" required disabled onchange="generateKodeAset();">
                                <option value="">Pilih Sub Kategori</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="merk_id-tambah" class="form-label">Merk</label>
                            <select class="form-select" id="merk_id-tambah" name="merk_id" required onchange="generateKodeAset();">
                                <option value="">Pilih Merk</option>
                                <?php foreach ($merk_list as $merk): ?>
                                    <option value="<?= $merk['id'] ?>"><?= esc($merk['nama_merk']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tipe_id-tambah" class="form-label">Tipe</label>
                            <select class="form-select" id="tipe_id-tambah" name="tipe_id" required disabled>
                                <option value="">Pilih Merk Dahulu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="serial_number-tambah" class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="serial_number-tambah" name="serial_number" placeholder="Contoh: XBN4503766" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="tahun_beli-tambah" class="form-label">Tahun Beli</label>
                            <input type="number" class="form-control" id="tahun_beli-tambah" name="tahun_beli" placeholder="Contoh: 2025" oninput="generateKodeAset();" required>
                        </div>
                        <div class="col-md-6">
                            <label for="harga_beli-tambah" class="form-label">Harga Beli</label>
                            <input type="number" class="form-control" id="harga_beli-tambah" name="harga_beli" placeholder="Contoh: 1500000">
                        </div>
                        <div class="col-md-6">
                            <label for="entitas_pembelian-tambah" class="form-label">Entitas Pembelian</label>
                            <input type="text" class="form-control" id="entitas_pembelian-tambah" name="entitas_pembelian" placeholder="Contoh: BANDAR INDONESIA" oninput="generateKodeAset();">
                        </div>
                        <div class="col-md-6">
                            <label for="status-tambah" class="form-label">Status Aset</label>
                            <select class="form-select" id="status-tambah" name="status" required>
                                <option value="Baik Terpakai" selected>Baik (Terpakai)</option>
                                <option value="Baik Tidak Terpakai">Baik (Tidak Terpakai)</option>
                                <option value="Rusak">Rusak</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="user_pengguna-tambah" class="form-label">User Pengguna</label>
                            <input type="text" class="form-control" id="user_pengguna-tambah" name="user_pengguna" oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="lokasi-tambah" class="form-label">Lokasi</label>
                            <select class="form-select" id="lokasi-tambah" name="lokasi_id" required>
                                <option value="">Pilih Lokasi</option>
                                <?php foreach ($lokasi_list as $lokasi): ?>
                                    <option value="<?= $lokasi['id'] ?>"><?= esc($lokasi['nama_lokasi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kode-tambah" class="form-label">Kode Aset (Otomatis)</label>
                            <input type="text" class="form-control" id="kode-tambah" name="kode" readonly style="background-color: #e9ecef;">
                        </div>
                        <div class="col-12">
                            <label for="keterangan-tambah" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan-tambah" name="keterangan" rows="3" oninput="this.value = this.value.toUpperCase();"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="bukti_aset" class="form-label">Dok. Aset (Maks. 2 Foto)</label>
                            <input type="file" class="form-control" id="bukti_aset" name="bukti_aset[]" multiple accept="image/png, image/jpeg, image/jpg, application/pdf">
                            <div class="form-text">Pilih hingga 2 file (jpg, png, pdf). Ukuran maks. 2MB per file.</div>
                        </div>
                    </div>
                    <div class="modal-footer mt-4">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    // --- LOGIKA UNTUK MENAMPILKAN POPUP QR CODE ---
    <?php if (session()->getFlashdata('new_aset')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const newAset = <?= json_encode(session()->getFlashdata('new_aset')) ?>;
            document.getElementById('qr-kode').textContent = newAset.kode;
            document.getElementById('qr-detail').textContent = `${newAset.entitas_pembelian} - ${newAset.nama_sub_kategori}`;
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

    function generateKodeAset() {
        // Ambil elemen dari form
        const entitasInput = document.getElementById('entitas_pembelian-tambah');
        const tahunInput = document.getElementById('tahun_beli-tambah');
        const subKategoriSelect = document.getElementById('sub_kategori_id-tambah');
        const merkSelect = document.getElementById('merk_id-tambah');
        const kodeInput = document.getElementById('kode-tambah');

        // Ambil nilai dan format
        const entitas = entitasInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 5);
        const tahun_beli = tahunInput.value;
        const subKategoriNama = subKategoriSelect.options[subKategoriSelect.selectedIndex]?.text.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 5) || 'SUB';
        const merkNama = merkSelect.options[merkSelect.selectedIndex]?.text.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 3) || 'MRK';

        // Tampilkan preview jika semua data kunci sudah diisi
        if (entitas && tahun_beli && subKategoriSelect.value && merkSelect.value) {
            // "XX" adalah placeholder untuk nomor unik yang akan dibuat di server
            kodeInput.value = `BTR/${entitas}/${tahun_beli}/${subKategoriNama}/${merkNama}/XX`;
        } else {
            kodeInput.value = '';
        }
    }


    // Dropdown Dinamis untuk Merk dan Tipe
    function setupMerkTipeDropdowns(merkSelectId, tipeSelectId, selectedTipeId = null) {
        const merkSelect = document.getElementById(merkSelectId);
        const tipeSelect = document.getElementById(tipeSelectId);

        if (!merkSelect || !tipeSelect) return;

        merkSelect.addEventListener('change', function() {
            const merkId = this.value;
            tipeSelect.innerHTML = '<option value="">Memuat...</option>';
            tipeSelect.disabled = true;

            if (merkId) {
                fetch(`<?= base_url('api/tipe/') ?>${merkId}`)
                    .then(response => response.json())
                    .then(data => {
                        tipeSelect.innerHTML = '<option value="">Pilih Tipe</option>';
                        if (data.length > 0) {
                            data.forEach(tipe => {
                                const option = document.createElement('option');
                                option.value = tipe.id;
                                option.textContent = tipe.nama_tipe;
                                if (selectedTipeId && selectedTipeId == tipe.id) {
                                    option.selected = true;
                                }
                                tipeSelect.appendChild(option);
                            });
                            tipeSelect.disabled = false;
                        } else {
                            tipeSelect.innerHTML = '<option value="">Tidak ada tipe untuk merk ini</option>';
                        }
                    });
            } else {
                tipeSelect.innerHTML = '<option value="">Pilih Merk Dahulu</option>';
                tipeSelect.disabled = true;
            }
        });
    }

    // Inisialisasi di halaman Data Aset & Dashboard (Modal Tambah)
    document.addEventListener('DOMContentLoaded', function() {
        setupMerkTipeDropdowns('merk_id-tambah', 'tipe_id-tambah');
    });

    // Inisialisasi di halaman Edit Aset
    document.addEventListener('DOMContentLoaded', function() {
        setupMerkTipeDropdowns('merk_id', 'tipe_id', '<?= $aset['tipe_id'] ?? '' ?>');
    });

    const subKategoris = <?= json_encode($subkategori_list) ?>;

    function populateSubKategori(kategoriId, selectedSubKategoriId = null) {
        const subKategoriSelect = document.getElementById('sub_kategori_id-tambah');
        subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
        subKategoriSelect.disabled = true;

        if (kategoriId) {
            const filteredSubkategoris = subKategoris.filter(sub => sub.kategori_id == kategoriId);
            if (filteredSubkategoris.length > 0) {
                filteredSubkategoris.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.nama_sub_kategori;
                    if (selectedSubKategoriId && selectedSubKategoriId == sub.id) {
                        option.selected = true;
                    }
                    subKategoriSelect.appendChild(option);
                });
                subKategoriSelect.disabled = false;
            }
        }
    }


    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk filter kategori
        const filterKategori = document.getElementById('filter-kategori');
        if (filterKategori) {
            filterKategori.addEventListener('change', function() {
                const url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('kategori_id', this.value);
                } else {
                    url.searchParams.delete('kategori_id');
                }
                window.location.href = url.toString();
            });
        }

       

         // --- Inisialisasi Select2 di modal ---
    $('#tambahAsetModal').on('shown.bs.modal', function () {
        $('#kategori_id-tambah, #sub_kategori_id-tambah, #merk_id-tambah, #tipe_id-tambah, #lokasi-tambah').select2({
            dropdownParent: $('#tambahAsetModal')
        });
    });

    // --- KODE UNTUK DROPDOWN DEPENDEN ---
    const kategoriTambahSelect = $('#kategori_id-tambah');
    const subKategoriTambahSelect = $('#sub_kategori_id-tambah');
    const merkTambahSelect = $('#merk_id-tambah');
    const tipeTambahSelect = $('#tipe_id-tambah');

        // Event untuk Kategori -> Sub Kategori
        kategoriTambahSelect.on('change', function () { // Menggunakan 'change' agar lebih umum
            const kategoriId = $(this).val();
            
            subKategoriTambahSelect.empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change');
            generateKodeAset();

            if (kategoriId) {
                const allSubKategoris = <?= json_encode($subkategori_list) ?>;
                const filteredSubkategoris = allSubKategoris.filter(sub => sub.kategori_id == kategoriId);

                subKategoriTambahSelect.empty().append('<option value="">Pilih Sub Kategori</option>');
                if (filteredSubkategoris.length > 0) {
                    filteredSubkategoris.forEach(sub => {
                        const option = new Option(sub.nama_sub_kategori, sub.id, false, false);
                        subKategoriTambahSelect.append(option);
                    });
                    subKategoriTambahSelect.prop('disabled', false);
                } else {
                    subKategoriTambahSelect.append('<option value="">Tidak ada sub kategori</option>');
                }
                subKategoriTambahSelect.trigger('change');
            }
        });

        // Event untuk Merk -> Tipe
        merkTambahSelect.on('change', function () { // Menggunakan 'change'
            const merkId = $(this).val();

            tipeTambahSelect.empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change');
            generateKodeAset();

            if (merkId) {
                fetch(`<?= base_url('api/tipe/') ?>${merkId}`)
                    .then(response => response.json())
                    .then(data => {
                        tipeTambahSelect.empty().append('<option value="">Pilih Tipe</option>');
                        if (data.length > 0) {
                            data.forEach(tipe => {
                                const option = new Option(tipe.nama_tipe, tipe.id, false, false);
                                tipeTambahSelect.append(option);
                            });
                            tipeTambahSelect.prop('disabled', false);
                        } else {
                            tipeTambahSelect.append('<option value="">Tidak ada tipe</option>');
                        }
                        tipeTambahSelect.trigger('change');
                    });
            }
        });

        // Panggil generateKodeAset saat dropdown lain juga berubah
        $('#sub_kategori_id-tambah, #tahun_beli-tambah, #entitas_pembelian-tambah').on('change', generateKodeAset);
    
        // --- FUNGSI PENCARIAN REAL-TIME DI DATA ASET ---
        const searchInput = document.getElementById('searchInput');
const tableBody = document.getElementById('asetTableBody');

if (searchInput && tableBody) {
    searchInput.addEventListener('keyup', function() {
        const keyword = this.value;
        // Pastikan URL fetch sudah benar
        fetch(`<?= base_url('aset/search') ?>?q=${keyword}`)
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = ''; // Kosongkan tabel sebelum mengisi hasil baru
                if (data.length > 0) {
                    data.forEach(aset => {
                        // Variabel untuk menangani data yang mungkin null
                        const serialNumber = aset.serial_number || '-';
                        const penanggungJawab = aset.user_pengguna || '-';

                        // Buat baris tabel dengan urutan kolom yang BENAR
                        const row = `<tr>
                            <td>${aset.kode}</td>
                            <td>${aset.nama_sub_kategori}</td>
                            <td>${aset.nama_merk}</td>
                            <td>${serialNumber}</td>
                            <td>${penanggungJawab}</td>
                            <td>${aset.nama_lokasi}</td>
                            <td><span class="badge bg-light text-dark">${aset.status}</span></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm view-detail" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="${aset.id}" title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <?php if (session()->get('role') === 'admin'): ?>
                                <a href="<?= base_url('aset/') ?>${aset.id}/edit" class="btn btn-warning btn-sm" title="Edit Aset">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="confirmDelete(this)" data-id="${aset.id}" data-kode="${aset.kode}" class="btn btn-danger btn-sm" title="Hapus Aset">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>`;
                        tableBody.innerHTML += row;
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center">Aset tidak ditemukan.</td></tr>`;
                }
            })
            .catch(error => console.error('Error searching:', error));
    });
}

         // --- LOGIKA MODAL DETAIL & RIWAYAT ASET ---
const detailAsetModal = document.getElementById('detailAsetModal');
        let currentAsetId = null;

        if (detailAsetModal) {
            detailAsetModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                currentAsetId = button.getAttribute('data-id');

                // Reset semua konten riwayat
                const verifikasiList = document.getElementById('timeline-verifikasi-list');
                const perubahanList = document.getElementById('timeline-perubahan-list');
                verifikasiList.innerHTML = '<li class="list-group-item">Memuat riwayat...</li>';
                perubahanList.innerHTML = '<li class="list-group-item">Memuat riwayat...</li>';

                // Fetch Detail Aset
                fetch(`/aset/${currentAsetId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('detail-kode').textContent = data.kode;
                        document.getElementById('detail-kategori').textContent = data.nama_kategori;
                        document.getElementById('detail-sub-kategori').textContent = data.nama_sub_kategori;
                        document.getElementById('detail-merk').textContent = data.nama_merk || '-';
                        document.getElementById('detail-type').textContent = data.nama_tipe || '-';
                        document.getElementById('detail-serial_number').textContent = data.serial_number || '-';
                        document.getElementById('detail-tahun_beli').textContent = data.tahun_beli;
                        document.getElementById('detail-harga_beli').textContent = formatRupiah(data.harga_beli);
                        document.getElementById('detail-entitas_pembelian').textContent = data.entitas_pembelian || '-';
                        document.getElementById('detail-user_pengguna').textContent = data.user_pengguna || '-';
                        document.getElementById('detail-lokasi').textContent = data.nama_lokasi || '-';
                        document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                        document.getElementById('detail-status').textContent = data.status;
                        // --- KODE BARU UNTUK MENAMPILKAN DOKUMENTASI ---
                        const dokumentasiContainer = document.getElementById('detail-dokumentasi');
                        dokumentasiContainer.innerHTML = ''; // Kosongkan dulu

                        if (data.dokumentasi && data.dokumentasi.length > 0) {
                            data.dokumentasi.forEach(doc => {
                                let docItem = '';
                                const fileUrl = `<?= base_url() ?>/${doc.path_file}`;

                                if (doc.tipe_file.startsWith('image/')) {
                                    docItem = `
                                        <div class="col-auto">
                                            <a href="${fileUrl}" target="_blank">
                                                <img src="${fileUrl}" alt="Bukti" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                            </a>
                                        </div>
                                    `;
                                } else {
                                    docItem = `
                                        <div class="col-auto">
                                            <a href="${fileUrl}" target="_blank" class="d-flex flex-column align-items-center justify-content-center img-thumbnail" style="width: 80px; height: 80px; text-decoration: none;">
                                                <i class="bi bi-file-earmark-pdf-fill" style="font-size: 2rem; color: #d33;"></i>
                                                <small class="text-muted mt-1">PDF</small>
                                            </a>
                                        </div>
                                    `;
                                }
                                dokumentasiContainer.innerHTML += docItem;
                            });
                        } else {
                            dokumentasiContainer.innerHTML = '<p class="text-muted small">Tidak ada dokumentasi aset yang diunggah.</p>';
                        }
                        // --- AKHIR KODE BARU ---
                    })
                    .catch(error => console.error('Error fetching detail:', error));

                // Fetch Riwayat Verifikasi (Stock Opname)
                fetch(`<?= base_url('aset/stockopname_history/') ?>${currentAsetId}`)
                    .then(response => response.json())
                    .then(data => {
                        verifikasiList.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const date = new Date(item.opname_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
                                const badge = item.ada_perubahan == '1' ? '<span class="badge bg-warning">Ada Usulan Perubahan</span>' : '<span class="badge bg-success">Data Sesuai</span>';
                                const listItem = `<li class="list-group-item"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1">Diverifikasi oleh: ${item.full_name}</h6><small>${date} WIB</small></div><p class="mb-1">Status verifikasi: ${badge}</p></li>`;
                                verifikasiList.innerHTML += listItem;
                            });
                        } else {
                            verifikasiList.innerHTML = '<li class="list-group-item">Belum ada riwayat verifikasi untuk aset ini.</li>';
                        }
                    })
                    .catch(error => console.error('Error fetching stock opname history:', error));
                
                // Fetch Riwayat Perubahan Data
                fetch(`<?= base_url('aset/history/') ?>${currentAsetId}`)
                    .then(response => response.json())
                    .then(data => {
                        perubahanList.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const proposed = JSON.parse(item.proposed_data);
                                let changes = '';
                                for (const key in proposed) {
                                    changes += `<span class="badge bg-secondary me-1">${key.replace('_', ' ')}: ${proposed[key]}</span>`;
                                }
                                const date = new Date(item.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
                                const listItem = `<li class="list-group-item"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1">Diajukan oleh: ${item.full_name}</h6><small>${date} WIB</small></div><p class="mb-1">Data yang diubah: ${changes}</p></li>`;
                                perubahanList.innerHTML += listItem;
                            });
                        } else {
                            perubahanList.innerHTML = '<li class="list-group-item">Tidak ada riwayat perubahan data untuk aset ini.</li>';
                        }
                    })
                    .catch(error => console.error('Error fetching change history:', error));
            });
        };

    })  
    

    // FUNGSI BARU UNTUK KONFIRMASI HAPUS
    function confirmDelete(el) {
        const asetId = el.getAttribute('data-id');
        const asetKode = el.getAttribute('data-kode');
        const deleteForm = document.getElementById('deleteForm');

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            html: `Anda akan menghapus aset dengan kode:<br><b>${asetKode}</b><br><br>Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Saja!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteForm.action = `<?= base_url('aset/') ?>${asetId}`;
                deleteForm.submit();
            }
        });
    }

    function formatRupiah(angka) {
        if(!angka) return 'Rp 0';
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + ribuan;
    }

    
    
</script>
<?= $this->endSection() ?>
