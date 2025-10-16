<?= $this->extend('layouts/main_dashboard') ?>


<?= $this->section('title') ?>
Data Aset
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* =================================
    CSS UNTUK FUNGSI CETAK (PRINT)
    =================================
    */

    @media print {
        /* Sembunyikan semua elemen di body secara default saat mencetak */
        body * {
            visibility: hidden;
        }

        /* Tampilkan hanya area yang memiliki kelas .print-area dan semua isinya */
        .print-area, .print-area * {
            visibility: visible;
        }

        /* Posisikan area cetak di bagian paling atas halaman cetak */
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Pengaturan untuk grid label stiker */
        .label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Pengaturan untuk setiap label stiker individual */
        .label {
            page-break-inside: avoid; /* Mencegah label terpotong antar halaman */
            width: 54mm; /* Sesuaikan dengan lebar stiker Anda */
            height: 30mm; /* Sesuaikan dengan tinggi stiker Anda */
            border: 1px dashed #999; /* Garis putus-putus untuk panduan potong */
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .label img { 
            width: 60px; 
            height: 60px; 
        }
        .label .kode-aset { 
            font-size: 8pt; 
            font-weight: bold; 
            margin: 0; 
            word-break: break-all; 
        }
        .label .detail-aset { 
            font-size: 6pt; 
            margin: 2px 0 0 0; 
        }

    }
    .table-responsive .table th,
    .table-responsive .table td {
        white-space: nowrap; /* Mencegah teks turun baris */
        min-width: 150px; /* Beri lebar minimal untuk setiap kolom */
    }
    /* Beri lebar khusus untuk kolom yang sering panjang */
    .table th:contains("KODE ASET"), .table td:nth-child(1),
    .table th:contains("SERIAL NUMBER"), .table td:nth-child(6) {
        min-width: 180px;
    }
    .table th:contains("KETERANGAN"), .table td:nth-child(13) {
        min-width: 250px;
        white-space: normal; /* Izinkan keterangan untuk turun baris */
    }
    .table th:contains("AKSI"), .table td:nth-child(14) {
        min-width: 80px;
    }

    /* =================================
    CSS UNTUK TAMPILAN 'BERBASIS DOKUMEN'
    =================================
    */

    /* Pengaturan untuk carousel mini di dalam sel tabel */
    .mini-carousel .carousel-content {
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mini-carousel img {
        max-height: 80px;
        max-width: 100%;
        cursor: pointer;
        object-fit: cover;
    }
    .mini-carousel .file-icon {
        font-size: 2.5rem;
        color: #6c757d; /* Warna ikon abu-abu */
    }

    /* =================================
    CSS UNTUK TAMPILAN 'BERBASIS QR CODE'
    =================================
    */
    #qrcode-view .qr-thumbnail,
    #qrcode-view .print-data img {
        width: 120px;
        min-width: 60px;
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }

    /* =================================
    CSS UNTUK FREEZE KOLOM'
    =================================
    */
    /* Memberi batas tinggi dan scroll pada pembungkus tabel */
    .table-responsive {
        max-height: 75vh; /* Maksimal tinggi 75% dari layar */
        overflow-y: auto; /* Menambahkan scroll vertikal */
    }

    /* Membuat header tabel menempel di atas */
    .table-responsive thead th {
        position: -webkit-sticky; /* Untuk Safari */
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff; /* Warna putih agar menyatu dengan card */
        box-shadow: inset 0 -2px 0 #dee2e6; /* Garis bawah visual */
    }

</style>
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


<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group" role="group" id="view-switcher">
        <button type="button" class="btn btn-primary active" data-view="aset"><i class="fas fa-server me-2"></i> Berbasis Aset</button>
        <button type="button" class="btn btn-outline-primary" data-view="dokumen"><i class="fas fa-file-alt me-2"></i> Berbasis Dokumen</button>
        <button type="button" class="btn btn-outline-primary" data-view="qrcode"><i class="fas fa-qrcode me-2"></i> Berbasis QR Code</button>
    </div>
</div>


<div id="view-container">

    <div id="aset-view">
    <div class="table-container shadow-sm">
        <div class="d-flex justify-content-end p-3">
            <a href="<?= base_url('aset/laporan/export') . '?' . http_build_query($filters ?? []) ?>" class="btn btn-success"><i class="bi bi-file-earmark-excel-fill me-2"></i>Ekspor ke Excel</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>KODE ASET</th>
                        <th>KATEGORI</th>
                        <th>SUB KATEGORI</th>
                        <th>MERK</th>
                        <th>TIPE</th>
                        <th>SERIAL NUMBER</th>
                        <th>HARGA BELI</th>
                        <th>ENTITAS PEMBELIAN</th>
                        <th>LOKASI</th>
                        <th>USER PENGGUNA</th>
                        <th>STATUS</th>
                        <th>UPDATE SO</th>
                        <th>KETERANGAN</th>
                        
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody id="asetTableBody">
                    <?php if (!empty($asets)): ?>
                        <?php foreach ($asets as $aset): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($aset['kode']) ?></td>
                                <td><?= esc($aset['nama_kategori']) ?></td>
                                <td><?= esc($aset['nama_sub_kategori']) ?></td>
                                <td><?= esc($aset['nama_merk']) ?></td>
                                <td><?= esc($aset['nama_tipe'] ?? '-') ?></td>
                                <td><?= esc($aset['serial_number'] ?: '-') ?></td>
                                <td><?= 'Rp ' . number_format($aset['harga_beli'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= esc($aset['entitas_pembelian'] ?: '-') ?></td>
                                <td><?= esc($aset['nama_lokasi']) ?></td>
                                <td><?= esc($aset['nama_karyawan'] ?? $aset['user_pengguna'] ?: '-') ?></td>
                                <td>
                                    <?php
                                    $status = $aset['status'];
                                    $badgeClass = '';

                                    if ($status == 'Baik Terpakai') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status == 'Baik Tidak Terpakai') {
                                        $badgeClass = 'bg-primary';
                                    } elseif ($status == 'Rusak' || $status == 'Perbaikan') {
                                        $badgeClass = 'bg-danger';
                                    } else {
                                        $badgeClass = 'bg-light text-dark'; // Warna default jika ada status lain
                                    }
                                    ?>
                                    <span class="badge fs-7 <?= $badgeClass ?>"><?= esc($status) ?></span>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($aset['last_so_date'])):
                                        // Ambil tanggal hari ini dan tanggal SO terakhir
                                        $today = new DateTime();
                                        $lastSoDate = new DateTime($aset['last_so_date']);
                                        
                                        // Hitung selisih bulan
                                        $interval = $today->diff($lastSoDate);
                                        $monthsSinceSo = ($interval->y * 12) + $interval->m;

                                        $badgeClass = '';
                                        $badgeText = esc(date('d M Y, H:i', strtotime($aset['last_so_date'])));

                                        // Tentukan warna badge berdasarkan selisih bulan
                                        if ($monthsSinceSo >= 6) {
                                            $badgeClass = 'bg-warning text-dark'; // Kuning jika sudah 6 bulan atau lebih
                                        } else {
                                            $badgeClass = 'bg-success'; // Hijau jika kurang dari 6 bulan
                                        }
                                    ?>
                                        <span class="badge fs-7 <?= $badgeClass ?>"><?= $badgeText ?></span>
                                    <?php else: ?>
                                        <span class="badge fs-7 bg-secondary">Belum SO</span>
                                    <?php endif; ?>
                                </td>

                                <td style="max-width: 200px; white-space: normal;"><?= esc($aset['keterangan'] ?: '-') ?></td>

                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item view-detail" type="button" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="<?= $aset['id'] ?>">
                                                    <i class="bi bi-eye-fill me-2"></i>Lihat Detail
                                                </button>
                                            </li>

                                            <?php if (!empty($aset['dokumen'])): ?>
                                            <li>
                                                <button class="dropdown-item view-docs" type="button" data-bs-toggle="modal" data-bs-target="#dokumenModal" data-nama-aset="<?= esc($aset['kode']); ?>" data-dokumen='<?= json_encode($aset['dokumen']); ?>'>
                                                    <i class="bi bi-paperclip me-2"></i>Lihat Dokumen
                                                </button>
                                            </li>
                                            <?php endif; ?>

                                            <?php if (session()->get('role') === 'admin'): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('aset/' . $aset['id'] . '/edit') ?>">
                                                    <i class="bi bi-pencil-fill me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDelete(this)" data-id="<?= $aset['id'] ?>" data-kode="<?= esc($aset['kode']) ?>">
                                                    <i class="bi bi-trash-fill me-2"></i>Hapus
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="14" class="text-center">Belum ada data aset.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <form action="" method="post" id="deleteForm">
                <?= csrf_field() ?><input type="hidden" name="_method" value="DELETE">
            </form>
        </div>
    </div>
</div>

    <div id="dokumen-view" style="display: none;">
        <div class="table-responsive shadow-sm">
            <table class="table table-bordered table-striped align-middle" style="min-width: 1200px;">
                <thead>
                    <tr>
                        <th style="width: 20%;">Dokumentasi</th>
                        <th>Kode Aset</th>
                        <th>Sub Kategori</th>
                        <th>Serial Number</th>
                        <th>Lokasi</th>
                        <th>User Pengguna</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($asets)): ?>
                        <?php foreach ($asets as $aset): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($aset['dokumen'])): ?>
                                        <div class="mini-carousel" data-dokumen='<?= json_encode($aset['dokumen']); ?>'>
                                            <div class="carousel-content text-center">
                                                </div>
                                            <div class="carousel-controls mt-1 text-center">
                                                <button class="btn btn-dark btn-sm prev-btn">&lt;</button>
                                                <span class="carousel-counter badge bg-secondary">1 / <?= count($aset['dokumen']); ?></span>
                                                <button class="btn btn-dark btn-sm next-btn">&gt;</button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada dokumen</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($aset['kode']); ?></td>
                                <td><?= esc($aset['nama_sub_kategori']); ?></td>
                                <td><?= esc($aset['serial_number'] ?: '-'); ?></td>
                                <td><?= esc($aset['nama_lokasi']); ?></td>
                                <td><?= esc($aset['nama_karyawan'] ?? $aset['user_pengguna']); ?></td>
                                <td><span class="badge bg-light text-dark"><?= esc($aset['status']); ?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item view-detail" type="button" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="<?= $aset['id'] ?>">
                                                    <i class="bi bi-eye-fill me-2"></i>Lihat Detail
                                                </button>
                                            </li>
                                            <?php if (session()->get('role') === 'admin'): ?>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('aset/' . $aset['id'] . '/edit') ?>">
                                                    <i class="bi bi-pencil-fill me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDelete(this)" data-id="<?= $aset['id'] ?>" data-kode="<?= esc($aset['kode']) ?>">
                                                    <i class="bi bi-trash-fill me-2"></i>Hapus
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">Tidak ada data aset ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <div id="qrcode-view" style="display: none;">
    <div class="table-container shadow-sm">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <span>Pilih aset yang ingin Anda cetak labelnya.</span>
            <button id="printButton" class="btn btn-success">
                <i class="bi bi-printer-fill me-2"></i>CETAK YANG DIPILIH
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox" id="selectAllCheckbox" class="form-check-input"></th>
                        <th style="width: 120px;">QR CODE</th>
                        <th>KODE ASET</th>
                        <th>ENTITAS</th>
                        <th>TAHUN BELI</th>
                        <th>KATEGORI & SUB</th>
                        <th>USER PENGGUNA</th>
                        <th>LOKASI</th>
                    </tr>
                </thead>
                <tbody id="barcodeTableBody">
                    <?php if (!empty($asets)): ?>
                        <?php foreach ($asets as $aset): ?>
                            <tr>
                                <td><input type="checkbox" class="form-check-input barcode-checkbox"></td>
                                <td>
                                    <?php if (!empty($aset['qrcode'])): ?>
                                        <img src="<?= base_url($aset['qrcode']) ?>" alt="QR Code" class="img-fluid qr-thumbnail">
                                        <div class="print-data d-none">
                                            <p class="kode-aset"><?= esc($aset['kode'] ?? 'N/A') ?></p>
                                            <p class="detail-aset"><?= esc($aset['nama_sub_kategori'] ?? 'N/A') ?></p> 
                                            <img src="<?= base_url($aset['qrcode']) ?>" alt="QR Code">
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= esc($aset['kode'] ?? 'N/A') ?></td>
                                <td><?= esc($aset['entitas_pembelian'] ?? 'N/A') ?></td>
                                <td><?= esc($aset['tahun_beli'] ?? 'N/A') ?></td>
                                <td>
                                    <div><?= esc($aset['nama_kategori'] ?? 'N/A') ?></div>
                                    <small class="text-muted"><?= esc($aset['nama_sub_kategori'] ?? 'N/A') ?></small>
                                </td>
                                <td><?= esc($aset['nama_karyawan'] ?? $aset['user_pengguna'] ?? 'N/A') ?></td>
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
                            <label for="bukti_aset" class="form-label">Dok. Aset (Maks. 2 File)</label>
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

<?= $this->section('modals') ?>
<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dokumenModalLabel">Dokumen untuk Aset: <span></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="dokumen-carousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner bg-light text-center" style="min-height: 400px;">
                        </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#dokumen-carousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#dokumen-carousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <div class="text-center mt-2">
                    <p id="dokumen-deskripsi" class="fw-bold mb-0">Nama Dokumen</p>
                    <small id="dokumen-counter" class="text-muted">1 / 1</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // =================================================================
    // BAGIAN 1: FUNGSI-FUNGSI GLOBAL (DEKLARASI FUNGSI)
    // =================================================================
    // Fungsi-fungsi ini bisa dipanggil dari mana saja di dalam halaman ini.

    /**
     * Mencetak area grid QR code pada tampilan "Berbasis QR Code".
     */
    function printQrView() {
        window.print();
    }

    /**
     * Mencetak QR code individual dari modal setelah menambah aset baru.
     */
    function printQrCode() {
        const printContent = document.getElementById('qrCodePrintArea').innerHTML;
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload();
    }

    /**
     * Membuat preview kode aset otomatis di form tambah aset.
     */
    function generateKodeAset() {
        const tahunBeli = $('#tahun_beli-tambah').val() || new Date().getFullYear();
        const subKategori = $('#sub_kategori_id-tambah option:selected').text().split(' (')[0] || 'SUBKAT';
        const merk = $('#merk_id-tambah option:selected').text() || 'MERK';

        // Format baru: Gabungkan Sub Kategori dan Merk
        const subKategoriMerk = subKategori.substring(0, 5).toUpperCase() + '_' + merk.substring(0, 3).toUpperCase();

        // Pastikan semua komponen kunci terisi sebelum menampilkan preview
        if (tahunBeli && $('#sub_kategori_id-tambah').val() && $('#merk_id-tambah').val()) {
            const kodeAset = 'BTR/' +
                tahunBeli + '/' +
                subKategori.substring(0, 5).toUpperCase() + '/' +
                merk.substring(0, 3).toUpperCase() +
                '/XX';
            $('#kode-tambah').val(kodeAset);
        } else {
            $('#kode-tambah').val(''); // Kosongkan jika belum lengkap
        }
    }
    
    /**
     * Menampilkan konfirmasi sebelum menghapus aset.
     */
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

    /**
     * Mengubah angka menjadi format Rupiah.
     */
    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + ribuan;
    }

    // =================================================================
    // BAGIAN 2: SCRIPT YANG DIJALANKAN SAAT HALAMAN SIAP (EVENT LISTENERS)
    // =================================================================
    $(document).ready(function() {

        // 1. Logika untuk checkbox "Pilih Semua"
const selectAllCheckbox = document.getElementById('selectAllCheckbox');
if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        // Cari semua checkbox di dalam Tampilan QR Code dan atur statusnya
        document.querySelectorAll('#qrcode-view .barcode-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
}

// 2. Logika untuk tombol "CETAK YANG DIPILIH"
const printButton = document.getElementById('printButton');
if (printButton) {
    printButton.addEventListener('click', function() {
        // Buat atau temukan area cetak yang tersembunyi
        let printArea = document.querySelector('.print-area');
        if (!printArea) {
            printArea = document.createElement('div');
            printArea.classList.add('print-area');
            document.body.appendChild(printArea);
        }
        printArea.innerHTML = ''; // Kosongkan area cetak setiap kali tombol diklik

        // Buat grid untuk menampung label
        const labelGrid = document.createElement('div');
        labelGrid.classList.add('label-grid');

        let selectedCount = 0;
        // Cari semua checkbox yang dicentang di dalam Tampilan QR Code
        document.querySelectorAll('#qrcode-view .barcode-checkbox:checked').forEach(checkbox => {
            selectedCount++;
            // Ambil data cetak tersembunyi dari baris tabel yang sama
            const row = checkbox.closest('tr');
            const printDataHTML = row.querySelector('.print-data').innerHTML;
            
            // Buat elemen label dan masukkan datanya
            const label = document.createElement('div');
            label.classList.add('label');
            label.innerHTML = printDataHTML;
            labelGrid.appendChild(label);
        });

        // 3. Cek apakah ada yang dipilih sebelum mencetak
        if (selectedCount > 0) {
            printArea.appendChild(labelGrid);
            window.print(); // Panggil fungsi cetak browser
        } else {
            // Tampilkan peringatan jika tidak ada yang dipilih
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Barcode Dipilih',
                text: 'Silakan pilih setidaknya satu barcode untuk dicetak.',
            });
        }
    });
}

        // --- Inisialisasi Tampilan Awal ---
        $('#view-container > div').hide();
        $('#aset-view').show();

        // --- LOGIKA UTAMA: PENGALIH TAMPILAN (VIEW SWITCHER) ---
        $('#view-switcher button').on('click', function() {
            $('#view-switcher button').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('active btn-primary');
            var viewToShow = $(this).data('view');
            $('#view-container > div').hide();
            $('#' + viewToShow + '-view').show();
            
            // Inisialisasi carousel mini jika pindah ke tampilan dokumen
            if (viewToShow === 'dokumen') {
                initializeMiniCarousels();
            }
        });

        // --- LOGIKA FITUR 1: POP-UP GALERI DOKUMEN ---
        $(document).on('click', '.view-docs', function() {
            var namaAset = $(this).data('nama-aset');
            var dokumen = $(this).data('dokumen');
            $('#dokumenModalLabel span').text(namaAset);
            var carouselInner = $('#dokumen-carousel .carousel-inner');
            carouselInner.empty();

            if (dokumen && dokumen.length > 0) {
                dokumen.forEach(function(doc, index) {
                    var activeClass = (index === 0) ? 'active' : '';
                    var slideHtml = '';
                    var fileUrl = `<?= base_url('files/bukti/') ?>${doc.path_file}`;

                    if (doc.tipe_file.startsWith('image/')) {
                        slideHtml = `<div class="carousel-item ${activeClass}" data-deskripsi="${doc.nama_asli_file}"><img src="${fileUrl}" class="d-block w-100" style="max-height: 500px; object-fit: contain;"></div>`;
                    } else {
                        slideHtml = `<div class="carousel-item ${activeClass}" data-deskripsi="${doc.nama_asli_file}"><div class="d-flex flex-column justify-content-center align-items-center" style="height: 400px;"><i class="bi bi-file-earmark-text-fill" style="font-size: 5rem;"></i><h4>${doc.nama_asli_file}</h4><a href="${fileUrl}" target="_blank" class="btn btn-primary mt-2">Lihat File</a></div></div>`;
                    }
                    carouselInner.append(slideHtml);
                });
                $('#dokumen-carousel .carousel-control-prev, #dokumen-carousel .carousel-control-next').toggle(dokumen.length > 1);
                updateCarouselInfo();
            } else {
                carouselInner.html('<div class="carousel-item active"><div class="text-center p-5">Tidak ada dokumen.</div></div>');
                $('#dokumen-carousel .carousel-control-prev, #dokumen-carousel .carousel-control-next').hide();
                $('#dokumen-deskripsi, #dokumen-counter').text('');
            }
        });

        function updateCarouselInfo() {
            var activeItem = $('#dokumen-carousel .carousel-item.active');
            if (!activeItem.length) return;
            var total = $('#dokumen-carousel .carousel-item').length;
            var current = activeItem.index() + 1;
            $('#dokumen-counter').text(`${current} / ${total}`);
            $('#dokumen-deskripsi').text(activeItem.data('deskripsi'));
        }
        $('#dokumen-carousel').on('slid.bs.carousel', updateCarouselInfo);

        // --- LOGIKA FITUR 2: CAROUSEL MINI DI TABEL DOKUMEN ---
        function initializeMiniCarousels() {
            $('.mini-carousel').each(function() {
                var carousel = $(this);
                if (carousel.hasClass('initialized')) return; // Mencegah inisialisasi ganda

                var dokumen = carousel.data('dokumen');
                if (!dokumen || dokumen.length === 0) return;
                var currentIndex = 0;

                function renderSlide(index) {
                    var doc = dokumen[index];
                    var fileUrl = `<?= base_url('files/bukti/') ?>${doc.path_file}`;
                    var contentHtml = doc.tipe_file.startsWith('image/')
                        ? `<img src="${fileUrl}" alt="${doc.nama_asli_file}" title="Klik untuk memperbesar">`
                        : `<a href="${fileUrl}" target="_blank" title="Lihat File"><i class="bi bi-file-earmark-text-fill file-icon"></i></a>`;
                    carousel.find('.carousel-content').html(contentHtml);
                    carousel.find('.carousel-counter').text(`${index + 1} / ${dokumen.length}`);
                }
                renderSlide(currentIndex);

                carousel.find('.prev-btn').off('click').on('click', function() {
                    currentIndex = (currentIndex > 0) ? currentIndex - 1 : dokumen.length - 1;
                    renderSlide(currentIndex);
                });
                carousel.find('.next-btn').off('click').on('click', function() {
                    currentIndex = (currentIndex < dokumen.length - 1) ? currentIndex + 1 : 0;
                    renderSlide(currentIndex);
                });
                carousel.find('.carousel-controls').toggle(dokumen.length > 1);
                carousel.addClass('initialized'); // Tandai sudah diinisialisasi
            });
        }

        // --- SEMUA LOGIKA LAMA ANDA YANG PERLU DIJALANKAN SAAT HALAMAN SIAP ---

        // Menampilkan popup QR Code setelah tambah aset
        <?php if (session()->getFlashdata('new_aset')): ?>
            const newAset = <?= json_encode(session()->getFlashdata('new_aset')) ?>;
            $('#qr-kode').text(newAset.kode);
            $('#qr-detail').text(`${newAset.entitas_pembelian} - ${newAset.nama_sub_kategori}`);
            $('#qr-image').attr('src', `<?= base_url() ?>/${newAset.qrcode}`);
            new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
        <?php endif; ?>

        // Inisialisasi Select2 di modal tambah
        $('#tambahAsetModal').on('shown.bs.modal', function () {
            $('#kategori_id-tambah, #sub_kategori_id-tambah, #merk_id-tambah, #tipe_id-tambah, #lokasi-tambah').select2({
                dropdownParent: $('#tambahAsetModal')
            });
        });

        // Logika Dropdown Kategori -> Sub Kategori
        $('#kategori_id-tambah').on('change', function () {
            const kategoriId = $(this).val();
            const subKategoriSelect = $('#sub_kategori_id-tambah');
            subKategoriSelect.empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change');
            if (kategoriId) {
                const allSubKategoris = <?= json_encode($subkategori_list) ?>;
                const filtered = allSubKategoris.filter(sub => sub.kategori_id == kategoriId);
                subKategoriSelect.empty().append('<option value="">Pilih Sub Kategori</option>');
                if (filtered.length > 0) {
                    filtered.forEach(sub => subKategoriSelect.append(new Option(sub.nama_sub_kategori, sub.id, false, false)));
                    subKategoriSelect.prop('disabled', false);
                }
                subKategoriSelect.trigger('change');
            }
        });

        // Logika Dropdown Merk -> Tipe
        $('#merk_id-tambah').on('change', function () {
            const merkId = $(this).val();
            const tipeSelect = $('#tipe_id-tambah');
            tipeSelect.empty().append('<option value="">Memuat...</option>').prop('disabled', true).trigger('change');
            if (merkId) {
                fetch(`<?= base_url('api/tipe/') ?>${merkId}`)
                    .then(response => response.json())
                    .then(data => {
                        tipeSelect.empty().append('<option value="">Pilih Tipe</option>');
                        if (data.length > 0) {
                            data.forEach(tipe => tipeSelect.append(new Option(tipe.nama_tipe, tipe.id, false, false)));
                            tipeSelect.prop('disabled', false);
                        }
                        tipeSelect.trigger('change');
                    });
            }
        });
        
        // Panggil generateKodeAset saat form berubah
        $('#sub_kategori_id-tambah, #merk_id-tambah, #tahun_beli-tambah').on('change', generateKodeAset);
        
        // Logika Pencarian Real-time
        $('#searchInput').on('keyup', function() {
            const keyword = this.value;
            const tableBody = $('#asetTableBody');
            fetch(`<?= base_url('aset/search') ?>?q=${keyword}`)
                .then(response => response.json())
                .then(data => {
                    tableBody.empty();
                    if (data.length > 0) {
                        data.forEach(aset => {
                            const row = `<tr>...</tr>`; // Bangun baris tabel seperti di PHP
                            tableBody.append(row);
                        });
                    } else {
                        tableBody.append(`<tr><td colspan="8" class="text-center">Aset tidak ditemukan.</td></tr>`);
                    }
                });
        });

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
                        document.getElementById('detail-tahun_beli').textContent = data.tahun_beli;z
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
                                const fileUrl = `<?= base_url('files/bukti/') ?>${doc.path_file}`;

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
    
    

    
    
</script>
<?= $this->endSection() ?>
