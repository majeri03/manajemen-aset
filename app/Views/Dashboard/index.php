<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="main-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-4">
            <h4 class="mb-0">Dashboard Overview</h4>
            <p class="text-muted small">Ringkasan cepat aset perusahaan Anda.</p>
        </div>
        <div class="col-md-6 col-lg-8 mt-3 mt-md-0 d-flex justify-content-end align-items-center flex-wrap">
            <form action="<?= base_url('aset') ?>" method="get" class="me-3">
                <div class="input-group search-bar">
                    <input type="text" class="form-control" name="keyword" placeholder="Cari aset...">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <button class="btn btn-custom-icon me-2" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                <i class="bi bi-plus-circle me-2"></i> Tambah Aset
            </button>
            <div class="dropdown me-2">
                <button class="btn btn-custom-icon dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i> Export Laporan
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(1)">Januari</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(2)">Februari</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(3)">Maret</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(4)">April</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(5)">Mei</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(6)">Juni</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(7)">Juli</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(8)">Agustus</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(9)">September</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(10)">Oktober</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(11)">November</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportLaporanBulanan(12)">Desember</a></li>
                </ul>
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


<div class="container-fluid">
    <div class="row g-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card shadow-sm blue">
            <div class="stat-card-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            <h6 class="stat-card-title">Total Jumlah Aset</h6>
            <h3 class="stat-card-value count-up" data-to="<?= $total_aset ?>"><?= $total_aset ?></h3>
            <p class="stat-card-detail">Unit Terdaftar</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card shadow-sm green">
            <div class="stat-card-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <h6 class="stat-card-title">Total Nilai Aset</h6>
            <h3 class="stat-card-value count-up" data-to="<?= $total_nilai_aset ?>">Rp <?= number_format($total_nilai_aset, 0, ',', '.') ?></h3>
            <p class="stat-card-detail">Berdasarkan Harga Beli</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card shadow-sm red">
            <div class="stat-card-icon">
                <i class="bi bi-wrench-adjustable"></i>
            </div>
            <h6 class="stat-card-title">Aset Rusak</h6>
            <h3 class="stat-card-value count-up" data-to="<?= $aset_rusak ?>"><?= $aset_rusak ?></h3>
            <p class="stat-card-detail">Unit Perlu Perbaikan</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card shadow-sm yellow">
            <div class="stat-card-icon">
                <i class="bi bi-bell-fill"></i>
            </div>
            <h6 class="stat-card-title">Permintaan Pending</h6>
            <h3 class="stat-card-value count-up" data-to="<?= $pending_requests ?>"><?= $pending_requests ?></h3>
            <p class="stat-card-detail">Menunggu Persetujuan</p>
        </div>
    </div>
</div>

    <div class="row mt-5 g-4">
        <div class="col-lg-6">
            <div class="table-container shadow-sm">
                <h5 class="mb-4">Distribusi Aset (Kategori)</h5>
                <div class="chart-container">
                    <canvas id="assetCategoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="table-container shadow-sm">
                <h5 class="mb-4">Status Aset</h5>
                <div class="chart-container">
                    <canvas id="assetStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>


<div class="container-fluid">
    <div class="table-container shadow-sm mt-5">
        <h5 class="mb-4">Aset Terbaru</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">KODE</th>
                        <th scope="col">KATEGORI BARANG</th>
                        <th scope="col">SUB KATEGORI</th>
                        <th scope="col">MERK</th>
                        <th scope="col">TIPE</th> <th scope="col">SERIAL NUMBER</th>
                        <th scope="col">TAHUN</th>
                  
                        <th scope="col">LOKASI</th>
                        <th scope="col">AKSI</th>
                    </tr>
                </thead>
                <tbody id="asetTableBody">
                    <?php if (!empty($asets)): ?>
                        <?php foreach ($asets as $aset): ?>
                            <tr>
                                <td><?= esc($aset['kode']) ?></td>
                                <td><?= esc($aset['nama_kategori']) ?></td>
                                <td><?= esc($aset['nama_sub_kategori']) ?></td>
                                <td><?= esc($aset['nama_merk']) ?></td> 
                                <td><?= esc($aset['nama_tipe']) ?></td> 
                                <td><?= esc($aset['serial_number']) ?></td>
                                <td><?= esc($aset['tahun']) ?></td>
                            
                                <td><?= esc($aset['nama_lokasi']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-detail" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailAsetModal"
                                            data-id="<?= $aset['id'] ?>">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data aset.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="detailAsetModal" tabindex="-1" aria-labelledby="detailAsetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!- Ubah ke modal-lg ->
        <div class="modal-content">
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
                        <p><strong>Tahun:</strong> <span id="detail-tahun"></span></p>
                        <p><strong>Harga Beli:</strong> <span id="detail-harga_beli"></span></p>
                        <p><strong>Entitas Pembelian:</strong> <span id="detail-entitas_pembelian"></span></p>
                        <p><strong>Penanggung Jawab:</strong> <span id="detail-penanggung_jawab"></span></p>
                        <p><strong>Lokasi:</strong> <span id="detail-lokasi"></span></p>
                        <p><strong>Status:</strong> <span id="detail-status"></span></p>
                    </div>
                </div>
                <p><strong>Keterangan:</strong> <span id="detail-keterangan"></span></p>
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
                <div class="mb-3">
                    <label for="kategori_id-tambah" class="form-label">Kategori Barang</label>
                    <select class="form-select" id="kategori_id-tambah" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori_list as $kategori): ?>
                            <option value="<?= $kategori['id'] ?>"><?= $kategori['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="sub_kategori_id-tambah" class="form-label">Sub Kategori</label>
                    <select class="form-select" id="sub_kategori_id-tambah" name="sub_kategori_id" required disabled onchange="generateKodeAset();">
                        <option value="">Pilih Sub Kategori</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="merk_id-tambah" class="form-label">Merk</label>
                    <select class="form-select" id="merk_id-tambah" name="merk_id" required onchange="generateKodeAset();">
                        <option value="">Pilih Merk</option>
                        <?php foreach ($merk_list as $merk): ?>
                            <option value="<?= $merk['id'] ?>"><?= esc($merk['nama_merk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tipe_id-tambah" class="form-label">Tipe</label>
                    <select class="form-select" id="tipe_id-tambah" name="tipe_id" required disabled>
                        <option value="">Pilih Merk Dahulu</option>
                    </select>
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
                    <label for="harga_beli-tambah" class="form-label">Harga Beli</label>
                    <input type="number" class="form-control" id="harga_beli-tambah" name="harga_beli" placeholder="Contoh: 1500000">
                </div>
                <div class="mb-3">
                    <label for="entitas_pembelian-tambah" class="form-label">Entitas Pembelian</label>
                    <input type="text" class="form-control" id="entitas_pembelian-tambah" name="entitas_pembelian" placeholder="Contoh: PT. JAYA ABADI" oninput="generateKodeAset();">
                </div>
                <div class="mb-3">
                    <label for="status-tambah" class="form-label">Status Aset</label>
                    <select class="form-select" id="status-tambah" name="status" required>
                        <option value="Baik Terpakai" selected>Baik (Terpakai)</option>
                        <option value="Baik Tidak Terpakai">Baik (Tidak Terpakai)</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="penanggung_jawab-tambah" class="form-label">Penanggung Jawab</label>
                    <input type="text" class="form-control" id="penanggung_jawab-tambah" name="penanggung_jawab" oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="mb-3">
                    <label for="lokasi-tambah" class="form-label">Lokasi</label>
                    <select class="form-select" id="lokasi-tambah" name="lokasi_id" required>
                        <option value="">Pilih Lokasi</option>
                        <?php foreach ($lokasi_list as $lokasi): ?>
                            <option value="<?= $lokasi['id'] ?>"><?= esc($lokasi['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                    </select>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.7/dist/countUp.min.js"></script>
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
        const tahunInput = document.getElementById('tahun-tambah');
        const subKategoriSelect = document.getElementById('sub_kategori_id-tambah');
        const merkSelect = document.getElementById('merk_id-tambah');
        const kodeInput = document.getElementById('kode-tambah');

        // Ambil nilai dan format
        const entitas = entitasInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 5);
        const tahun = tahunInput.value;
        const subKategoriNama = subKategoriSelect.options[subKategoriSelect.selectedIndex]?.text.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 5) || 'SUB';
        const merkNama = merkSelect.options[merkSelect.selectedIndex]?.text.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 3) || 'MRK';

        // Tampilkan preview jika semua data kunci sudah diisi
        if (entitas && tahun && subKategoriSelect.value && merkSelect.value) {
            // "XX" adalah placeholder untuk nomor unik yang akan dibuat di server
            kodeInput.value = `BTR/${entitas}/${tahun}/${subKategoriNama}/${merkNama}/XX`;
        } else {
            kodeInput.value = '';
        }
    }
    

    const allSubKategoris = <?= json_encode($subkategori_list) ?>;
    
    function populateSubKategori(kategoriId, subKategoriSelect, selectedSubKategoriId = null) {
        subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
        subKategoriSelect.disabled = true;

        if (kategoriId) {
            const filteredSubkategoris = allSubKategoris.filter(sub => sub.kategori_id == kategoriId);
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


    function searchAset() {
        const keyword = document.getElementById('searchInput').value;
        if (keyword) {
            window.location.href = `<?= base_url('aset') ?>?keyword=${encodeURIComponent(keyword)}`;
        }
    }

    function exportLaporanBulanan(bulan) {
        window.location.href = `<?= base_url('dashboard/export/') ?>${bulan}`;
    }

    // Inisialisasi Chart.js
    
    const labels = <?= json_encode($chartLabels) ?>;
    const data = <?= json_encode($chartData) ?>;

    function generateRandomColor() {
        const r = Math.floor(Math.random() * 200);
        const g = Math.floor(Math.random() * 200);
        const b = Math.floor(Math.random() * 200);
        return `rgb(${r}, ${g}, ${b})`;
    }

    const dynamicColors = labels.map(() => generateRandomColor());

    var ctx1 = document.getElementById('assetCategoryChart').getContext('2d');
    var assetCategoryChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Aset',
                data: data,
                backgroundColor: dynamicColors,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false,
                }
            }
        }
    });


const statusLabels = <?= json_encode($statusLabels) ?>;
const statusData = <?= json_encode($statusData) ?>;
// [MODIFIED] Status colors for chart
const statusColors = {
    'BAIK TERPAKAI': '#065f46',
    'BAIK TIDAK TERPAKAI': '#0694a2',
    'RUSAK': '#991b1b',
};

const dynamicStatusColors = statusLabels.map(label => statusColors[label.toUpperCase()] || '#6c757d');
var ctx2 = document.getElementById('assetStatusChart').getContext('2d');
var assetStatusChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: statusLabels,
        datasets: [{
            label: 'Jumlah Aset',
            data: statusData,
            backgroundColor: dynamicStatusColors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: { display: false },
            title: { display: false }
        }
    }
});


window.onload = function() {
    // 1. Skrip ini mencari semua elemen dengan class 'count-up'
    const countUpElements = document.querySelectorAll('.count-up');
    
    countUpElements.forEach(el => {
        // 2. Mengambil angka target dari atribut 'data-to'
        const endValue = el.getAttribute('data-to');
        
        // 3. Mengecek apakah teksnya mengandung 'Rp' untuk format Rupiah
        const isRupiah = el.innerText.includes('Rp');
        let instance;

        if (isRupiah) {
            // Jika ya, format sebagai mata uang
            instance = new CountUp(el, endValue, {
                prefix: 'Rp ',
                separator: '.',
                decimal: ',',
                duration: 2.5
            });
        } else {
            // Jika tidak, tambahkan akhiran seperti 'Unit' atau 'Permintaan'
            instance = new CountUp(el, endValue, {
                suffix: ' ' + (el.innerText.split(' ')[1] || ''),
                duration: 2.5
            });
        }

        if (!instance.error) {
            instance.start();
        } else {
            console.error(instance.error);
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.querySelector('.search-bar .btn');

    if (searchInput) {
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                searchAset();
            }
        });
    }
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            searchAset();
        });
    }

    const detailAsetModal = document.getElementById('detailAsetModal');
        let currentAsetId = null;

        if (detailAsetModal) {
            const riwayatBtn = detailAsetModal.querySelector('#lihat-riwayat-btn');
            const timelineContainer = detailAsetModal.querySelector('#timeline-container');
            const timelineList = detailAsetModal.querySelector('#timeline-list');
            
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
                document.getElementById('detail-tahun').textContent = data.tahun;
                document.getElementById('detail-harga_beli').textContent = formatRupiah(data.harga_beli);
                document.getElementById('detail-entitas_pembelian').textContent = data.entitas_pembelian || '-';
                document.getElementById('detail-penanggung_jawab').textContent = data.penanggung_jawab || '-';
                document.getElementById('detail-lokasi').textContent = data.nama_lokasi || '-';
                document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                document.getElementById('detail-status').textContent = data.status;
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
        riwayatBtn.addEventListener('click', function() {
            if (!currentAsetId) return;
            timelineList.innerHTML = '<li class="list-group-item">Memuat riwayat...</li>';
            timelineContainer.style.display = 'block';
            fetch(`<?= base_url('aset/history/') ?>${currentAsetId}`)
                .then(response => response.json())
                .then(data => {
                    timelineList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const proposed = JSON.parse(item.proposed_data);
                            let changes = '';
                            for (const key in proposed) {
                                changes += `<span class="badge bg-secondary me-1">${key.replace('_', ' ')}: ${proposed[key]}</span>`;
                            }
                            const date = new Date(item.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' });
                            const listItem = `<li class="list-group-item"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1">Perubahan oleh: ${item.full_name}</h6><small>${date} WIB</small></div><p class="mb-1">Data yang diubah: ${changes}</p></li>`;
                            timelineList.innerHTML += listItem;
                        });
                    } else {
                        timelineList.innerHTML = '<li class="list-group-item">Tidak ada riwayat perubahan untuk aset ini.</li>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching history:', error);
                    timelineList.innerHTML = '<li class="list-group-item text-danger">Gagal memuat riwayat.</li>';
                });
        });
    }

    const kategoriTambahSelect = document.getElementById('kategori_id-tambah');
    if (kategoriTambahSelect) {
        kategoriTambahSelect.addEventListener('change', function() {
            populateSubKategori(this.value, document.getElementById('sub_kategori_id-tambah'));
            generateKodeAset();
        });
    }
    const subKategoriTambahSelect = document.getElementById('sub_kategori_id-tambah');
    if (subKategoriTambahSelect) {
        subKategoriTambahSelect.addEventListener('change', generateKodeAset);
    }
})


function formatRupiah(angka) {
    if(!angka) return 'Rp 0';
    var reverse = angka.toString().split('').reverse().join(''),
        ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return 'Rp ' + ribuan;
}

    // Fungsi untuk dropdown dinamis Merk -> Tipe
    function setupMerkTipeDropdowns(merkSelectId, tipeSelectId) {
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
    setupMerkTipeDropdowns('merk_id-tambah', 'tipe_id-tambah');
});

</script>
<?= $this->endSection() ?>

