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
                    <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
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

                                            <?php if (!empty($aset['berkas_list'])): ?>
                                                <li>
                                                    <button class="dropdown-item view-docs" type="button" data-bs-toggle="modal" data-bs-target="#dokumenModal" data-nama-aset="<?= esc($aset['kode']); ?>" data-dokumen='<?= json_encode($aset['berkas_list']); ?>'>
                                                        <i class="bi bi-paperclip me-2"></i>Lihat Berkas Legal
                                                    </button>
                                                </li>
                                            <?php endif; ?>

                                            <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
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
                                        <span class="text-muted small">Tidak ada dokumentasi</span>
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
                                            <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
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
    <div id="printArea" class="print-area d-none"></div>
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
<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dokumenModalLabel">Berkas Legal untuk Aset: <span></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="berkas-list-container">
                    </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    // SCRIPT YANG DIJALANKAN SAAT HALAMAN SIAP
    // =================================================================
    $(document).ready(function() {

        // --- Inisialisasi Tampilan Awal ---
        $('#view-container > div').hide();
        $('#aset-view').show();

        // --- PENGALIH TAMPILAN (VIEW SWITCHER) ---
        $('#view-switcher button').on('click', function() {
            $('#view-switcher button').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('active btn-primary');
            var viewToShow = $(this).data('view');
            $('#view-container > div').hide();
            $('#' + viewToShow + '-view').show();

            if (viewToShow === 'dokumen') {
                initializeMiniCarousels();
            }
        });

        function initializeMiniCarousels() {
            document.querySelectorAll('.mini-carousel').forEach(carouselEl => {
                const dokumenData = JSON.parse(carouselEl.getAttribute('data-dokumen') || '[]');
                const contentDiv = carouselEl.querySelector('.carousel-content');
                const counterSpan = carouselEl.querySelector('.carousel-counter');
                const prevBtn = carouselEl.querySelector('.prev-btn');
                const nextBtn = carouselEl.querySelector('.next-btn');
                let currentIndex = 0;

                function showDokumen(index) {
                    contentDiv.innerHTML = ''; // Kosongkan konten sebelumnya
                    if (dokumenData.length === 0) {
                        contentDiv.innerHTML = '<span class="text-muted small">Tidak ada dokumen</span>';
                        if (counterSpan) counterSpan.textContent = '0 / 0';
                        if (prevBtn) prevBtn.style.display = 'none';
                        if (nextBtn) nextBtn.style.display = 'none';
                        return;
                    }
                    currentIndex = (index + dokumenData.length) % dokumenData.length;
                    const doc = dokumenData[currentIndex];
                    const fileUrl = `<?= base_url('files/bukti/') ?>${doc.path_file}`; // Sesuaikan URL ini

                    let itemHtml = '';
                    if (doc.tipe_file && doc.tipe_file.startsWith('image/')) {
                        itemHtml = `<a href="${fileUrl}" target="_blank" title="${doc.nama_asli_file || ''}">
                                        <img src="${fileUrl}" alt="${doc.nama_asli_file || 'Gambar Aset'}" class="img-fluid">
                                    </a>`;
                    } else if (doc.tipe_file && doc.tipe_file === 'application/pdf') {
                        itemHtml = `<a href="${fileUrl}" target="_blank" title="${doc.nama_asli_file || ''}" class="d-flex flex-column align-items-center text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger file-icon"></i>
                                        <small class="text-muted mt-1 text-truncate" style="max-width: 80%;">${doc.nama_asli_file || 'Lihat PDF'}</small>
                                    </a>`;
                    } else {
                        itemHtml = `<a href="${fileUrl}" target="_blank" title="${doc.nama_asli_file || ''}" class="d-flex flex-column align-items-center text-decoration-none">
                                        <i class="bi bi-file-earmark-text file-icon"></i>
                                        <small class="text-muted mt-1 text-truncate" style="max-width: 80%;">${doc.nama_asli_file || 'Lihat File'}</small>
                                    </a>`;
                    }
                    contentDiv.innerHTML = itemHtml;

                    if (counterSpan) counterSpan.textContent = `${currentIndex + 1} / ${dokumenData.length}`;
                    const showControls = dokumenData.length > 1;
                    if (prevBtn) prevBtn.style.display = showControls ? 'inline-block' : 'none';
                    if (nextBtn) nextBtn.style.display = showControls ? 'inline-block' : 'none';
                    if (counterSpan && !showControls) counterSpan.style.display = 'none'; 
                     else if (counterSpan) counterSpan.style.display = 'inline-block';

                }

                if (prevBtn) {
                    prevBtn.onclick = () => showDokumen(currentIndex - 1);
                }
                if (nextBtn) {
                    nextBtn.onclick = () => showDokumen(currentIndex + 1);
                }

                showDokumen(0);
            });
        }

        if ($('#view-switcher button[data-view="dokumen"]').hasClass('active')) {
            initializeMiniCarousels();
        }
        // --- LOGIKA POP-UP BERKAS LEGAL ---
        $(document).on('click', '.view-docs', function() {
            const namaAset = $(this).data('nama-aset');
            const berkas_list = $(this).data('dokumen');
            const modalTitle = $('#dokumenModalLabel span');
            const listContainer = $('#berkas-list-container');

            modalTitle.text(namaAset);
            listContainer.empty();

            if (berkas_list && berkas_list.length > 0) {
                berkas_list.forEach(function(berkas) {
                    let ikon = 'bi-file-earmark-text';
                    if (berkas.tipe_file && berkas.tipe_file.includes('pdf')) {
                        ikon = 'bi-file-earmark-pdf-fill text-danger';
                    } else if (berkas.tipe_file && berkas.tipe_file.startsWith('image/')) {
                        ikon = 'bi-image text-success';
                    }
                    const fileUrl = `<?= base_url('files/bukti/') ?>${berkas.path_file}`;
                    const listItem = `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi ${ikon} me-2"></i>
                                <strong>${berkas.nama_berkas}</strong>
                            </div>
                            <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye-fill me-1"></i> Lihat
                            </a>
                        </li>
                    `;
                    listContainer.append(listItem);
                });
            } else {
                listContainer.append('<li class="list-group-item text-center text-muted">Tidak ada berkas legal untuk aset ini.</li>');
            }
        });

        // --- MENAMPILKAN POP-UP QR CODE SETELAH TAMBAH ASET ---
        <?php if (session()->getFlashdata('new_aset')): ?>
            const newAset = <?= json_encode(session()->getFlashdata('new_aset')) ?>;
            $('#qr-kode').text(newAset.kode);
            $('#qr-detail').text(`${newAset.entitas_pembelian || ''} - ${newAset.nama_sub_kategori || ''}`);
            $('#qr-image').attr('src', `<?= base_url() ?>/${newAset.qrcode}`);
            new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
        <?php endif; ?>

        // --- INISIALISASI SELECT2 DI MODAL TAMBAH ---
        $('#tambahAsetModal').on('shown.bs.modal', function () {
            $('#kategori_id-tambah, #sub_kategori_id-tambah, #merk_id-tambah, #tipe_id-tambah, #lokasi-tambah').select2({
                dropdownParent: $('#tambahAsetModal')
            });
        });

        // --- LOGIKA DROPDOWN BERGANTUNG ---
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
        
        // --- EVENT UNTUK GENERATE KODE ASET ---
        $('#sub_kategori_id-tambah, #merk_id-tambah, #tahun_beli-tambah').on('change', generateKodeAset);
        
        // --- LOGIKA MODAL DETAIL & RIWAYAT ASET ---
        const detailAsetModal = document.getElementById('detailAsetModal');
        if (detailAsetModal) {
            detailAsetModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const currentAsetId = button.getAttribute('data-id');
                const verifikasiList = document.getElementById('timeline-verifikasi-list');
                const perubahanList = document.getElementById('timeline-perubahan-list');
                verifikasiList.innerHTML = '<li class="list-group-item">Memuat...</li>';
                perubahanList.innerHTML = '<li class="list-group-item">Memuat...</li>';

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
                        const filesContainer = document.getElementById('detail-dokumentasi');
                        filesContainer.innerHTML = ''; 
                        if (data.all_files && data.all_files.length > 0) {
                            data.all_files.forEach(file => {
                                let fileItemHTML = '';
                                const fileUrl = `<?= base_url('files/bukti/') ?>${file.path_file}`;

                                if (file.jenis === 'gambar' || (file.tipe_file && file.tipe_file.startsWith('image/'))) {
                                    fileItemHTML = `
                                        <div class="col-auto">
                                            <a href="${fileUrl}" target="_blank" title="${file.nama_file || ''}">
                                                <img src="${fileUrl}" alt="${file.nama_file || 'Gambar Aset'}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                            </a>
                                        </div>
                                    `;
                                } else if (file.jenis === 'berkas' || (file.tipe_file && file.tipe_file === 'application/pdf')) {
                                    fileItemHTML = `
                                        <div class="col-auto">
                                            <a href="${fileUrl}" target="_blank" title="${file.nama_file || ''}" class="d-flex flex-column align-items-center justify-content-center img-thumbnail" style="width: 80px; height: 80px; text-decoration: none;">
                                                <i class="bi bi-file-earmark-pdf-fill" style="font-size: 2rem; color: #d33;"></i>
                                                <small class="text-muted mt-1 text-truncate" style="max-width: 70px;">${file.nama_file || 'PDF'}</small>
                                            </a>
                                        </div>
                                    `;
                                } else {
                                    fileItemHTML = `
                                        <div class="col-auto">
                                            <a href="${fileUrl}" target="_blank" title="${file.nama_file || ''}" class="d-flex flex-column align-items-center justify-content-center img-thumbnail" style="width: 80px; height: 80px; text-decoration: none;">
                                                <i class="bi bi-file-earmark-text" style="font-size: 2rem; color: #6c757d;"></i>
                                                <small class="text-muted mt-1 text-truncate" style="max-width: 70px;">${file.nama_file || 'File'}</small>
                                            </a>
                                        </div>
                                    `;
                                }
                                filesContainer.innerHTML += fileItemHTML;
                            });
                        } else {
                            filesContainer.innerHTML = '<p class="text-muted small col-12">Tidak ada dokumentasi atau berkas yang diunggah.</p>';
                        }
                    });

                // Fetch Riwayat Verifikasi
                fetch(`<?= base_url('aset/stockopname_history/') ?>${currentAsetId}`)
                    .then(response => response.json())
                    .then(data => {
                        verifikasiList.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const date = new Date(item.opname_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
                                const badge = item.ada_perubahan == '1' ? '<span class="badge bg-warning">Ada Usulan</span>' : '<span class="badge bg-success">Sesuai</span>';
                                verifikasiList.innerHTML += `<li class="list-group-item"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1">Oleh: ${item.full_name}</h6><small>${date}</small></div><p class="mb-1">Status: ${badge}</p></li>`;
                            });
                        } else {
                            verifikasiList.innerHTML = '<li class="list-group-item">Belum ada riwayat verifikasi.</li>';
                        }
                    });
                
                // Fetch Riwayat Perubahan
                fetch(`<?= base_url('aset/history/') ?>${currentAsetId}`)
                    .then(response => response.json())
                    .then(data => {
                        perubahanList.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const proposed = JSON.parse(item.proposed_data);
                                let changes = '';
                                for (const key in proposed) {
                                    changes += `<span class="badge bg-secondary me-1">${key.replace('_',' ')}: ${proposed[key]}</span> `;
                                }
                                const date = new Date(item.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
                                perubahanList.innerHTML += `<li class="list-group-item"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1">Oleh: ${item.full_name}</h6><small>${date}</small></div><p class="mb-1">Perubahan: ${changes}</p></li>`;
                            });
                        } else {
                            perubahanList.innerHTML = '<li class="list-group-item">Tidak ada riwayat perubahan data.</li>';
                        }
                    });
            });
        };
    });
</script>
<script>
  $(function() {
    const tbody      = document.getElementById('barcodeTableBody');
    const selectAll  = document.getElementById('selectAllCheckbox');
    const printBtn   = document.getElementById('printButton');
    const printArea  = document.getElementById('printArea');

    function updatePrintState() {
      if (!tbody || !printBtn) return;
      const checked = tbody.querySelectorAll('.barcode-checkbox:checked').length;
      const disabled = checked === 0;
      printBtn.disabled = disabled;
      printBtn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
      printBtn.classList.toggle('disabled', disabled);
    }

    if (tbody) {
      tbody.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('barcode-checkbox')) {
          updatePrintState();
        }
      });
    }

    if (selectAll && tbody) {
      selectAll.addEventListener('change', function() {
        const checked = this.checked;
        tbody.querySelectorAll('.barcode-checkbox').forEach(cb => { cb.checked = checked; });
        updatePrintState();
      });
    }

    if (printBtn && tbody) {
      printBtn.addEventListener('click', function() {
        const rows = Array.from(tbody.querySelectorAll('.barcode-checkbox:checked'))
          .map(cb => cb.closest('tr'))
          .filter(Boolean);
        if (rows.length === 0) return;

        let html = '<div class="label-grid">';
        rows.forEach(row => {
          const data = row.querySelector('.print-data');
         if (data) html += '<div class="label">' + data.innerHTML + '</div>';
        });
        html += '</div>';

        if (printArea) {

          printArea.innerHTML = html;
          printArea.classList.remove('d-none');

          const images = printArea.querySelectorAll('img');
          const imageLoadPromises = [];

          if (images.length === 0) {
            window.print();
            setTimeout(() => {
                printArea.innerHTML = '';
                printArea.classList.add('d-none');
            }, 500);
            return; 
          }

          images.forEach(img => {
            const promise = new Promise((resolve, reject) => {
              if (img.complete) {
                resolve();
              } else {
                img.onload = resolve;
                img.onerror = reject;
              }
            });
            imageLoadPromises.push(promise);
          });

          Promise.all(imageLoadPromises)
            .then(() => {
              window.print();

              setTimeout(() => {
                printArea.innerHTML = '';
                printArea.classList.add('d-none');
              }, 500);
            })
            .catch(error => {
              console.error('Gagal memuat salah satu gambar QR code untuk dicetak:', error);
              alert('Gagal memuat gambar QR code. Coba lagi.');
              
              printArea.innerHTML = '';
              printArea.classList.add('d-none');
            });
          
        } else {
          const w = window.open('', '_blank');
          w.document.write('<!doctype html><html><head><meta charset="utf-8"><title>Cetak Label</title></head><body class="print-area">' + html + '</body></html>');
          w.document.close(); w.focus(); w.print(); w.close();
        }
      });
    }

    updatePrintState();
  });
</script>
<?= $this->endSection() ?>
