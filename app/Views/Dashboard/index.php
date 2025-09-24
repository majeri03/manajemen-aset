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
            <a href="<?= base_url('tracking') ?>" class="btn btn-custom-icon"><i class="bi bi-geo-alt me-2"></i> Tracking Aset</a>
        </div>
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
        <div class="col-md-6 col-lg-4">
            <div class="summary-card shadow-sm">
                <div class="card-icon total-aset">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="card-text">
                    <h6 class="text-muted">Total Jumlah Aset</h6>
                    <h4 class="count-up" data-to="<?= $total_aset ?>"><?= $total_aset ?> Unit</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="summary-card shadow-sm">
                <div class="card-icon aset-rusak">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
                <div class="card-text">
                    <h6 class="text-muted">Aset Rusak</h6>
                    <h4 class="count-up" data-to="<?= $aset_rusak ?>"><?= $aset_rusak ?> Unit</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="summary-card shadow-sm">
                <div class="card-icon pengguna"> <i class="bi bi-bell-fill"></i> </div>
                <div class="card-text">
                    <h6 class="text-muted">Permintaan Perubahan</h6>
                    <h4 class="count-up" data-to="<?= $pending_requests ?>"><?= $pending_requests ?> Permintaan</h4>
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
                        <th scope="col">SERIAL NUMBER</th>
                        <th scope="col">TAHUN</th>
                        <th scope="col">LOKASI</th>
                        <th scope="col">KETERANGAN</th>
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
                                <td><?= esc($aset['merk']) ?></td>
                                <td><?= esc($aset['serial_number']) ?></td>
                                <td><?= esc($aset['tahun']) ?></td>
                                <td><?= esc($aset['lokasi_id']) ?></td>
                                <td><?= esc($aset['keterangan']) ?></td>
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
                <p><strong>Tahun:</strong> <span id="detail-tahun"></span></p>
                <p><strong>Harga Beli:</strong> <span id="detail-harga_beli"></span></p>
                <p><strong>Entitas Pembelian:</strong> <span id="detail-entitas_pembelian"></span></p>
                <p><strong>Lokasi:</strong> <span id="detail-lokasi"></span></p>
                <p><strong>Keterangan:</strong> <span id="detail-keterangan"></span></p>
                <p><strong>Status:</strong> <span id="detail-status"></span></p>
                <hr>
                <p>
                    <strong>Terakhir Diperbarui:</strong> <span id="detail-updated_at"></span>
                    <button id="lihat-riwayat-btn" class="btn btn-sm btn-outline-primary ms-2">Lihat Riwayat Lengkap</button>
                </p>

                <div id="timeline-container" class="mt-3" style="display: none;">
                    <ul class="list-group" id="timeline-list">
                        </ul>
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
                    <input type="hidden" name="redirect_to" value="dashboard">
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
                        <select class="form-select" id="sub_kategori_id-tambah" name="sub_kategori_id" required disabled>
                            <option value="">Pilih Sub Kategori</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="merk-tambah" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="merk-tambah" name="merk" placeholder="Contoh: EPSON" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="type-tambah" class="form-label">Type</label>
                        <input type="text" class="form-control" id="type-tambah" name="type" placeholder="Contoh: L3110" oninput="this.value = this.value.toUpperCase();">
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
                        <input type="text" class="form-control" id="entitas_pembelian-tambah" name="entitas_pembelian" placeholder="Contoh: PT. JAYA ABADI">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.7/dist/countUp.min.js"></script>
<script>
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

    function generateKodeAset() {
        const kategoriSelect = document.getElementById('kategori_id-tambah');
        const subKategoriSelect = document.getElementById('sub_kategori_id-tambah');
        const tahun = document.getElementById('tahun-tambah').value;
        const merk = document.getElementById('merk-tambah').value.toUpperCase().replace(/\s+/g, '').substring(0, 3);
        
        const kategoriNama = kategoriSelect.options[kategoriSelect.selectedIndex]?.text.toUpperCase().replace(/\s+/g, '').substring(0, 5);
        const subKategoriNama = subKategoriSelect.options[subKategoriSelect.selectedIndex]?.text.toUpperCase().replace(/\s+/g, '').substring(0, 5);

        if (kategoriNama && subKategoriNama && tahun && merk) {
            document.getElementById('kode-tambah').value = `BTR/${kategoriNama}/${subKategoriNama}/${tahun}/${merk}`;
        } else {
            document.getElementById('kode-tambah').value = '';
        }
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

const detailAsetModal = document.getElementById('detailAsetModal');
if (detailAsetModal) {
    detailAsetModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const asetId = button.getAttribute('data-id');
        
        fetch(`/aset/${asetId}`) 
            .then(response => response.json())
            .then(data => {
                document.getElementById('detail-kode').textContent = data.kode;
                document.getElementById('detail-kategori').textContent = data.nama_kategori;
                document.getElementById('detail-sub-kategori').textContent = data.nama_sub_kategori;
                document.getElementById('detail-merk').textContent = data.merk;
                document.getElementById('detail-type').textContent = data.type || '-';
                document.getElementById('detail-serial_number').textContent = data.serial_number || '-';
                document.getElementById('detail-tahun').textContent = data.tahun;
                document.getElementById('detail-harga_beli').textContent = formatRupiah(data.harga_beli);
                document.getElementById('detail-entitas_pembelian').textContent = data.entitas_pembelian || '-';
                document.getElementById('detail-lokasi').textContent = data.lokasi;
                document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                document.getElementById('detail-status').textContent = data.status;
                document.getElementById('detail-updated_at').textContent = data.updated_at;
            })
            .catch(error => console.error('Error fetching detail:', error));
    });
}

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
    const countUpElements = document.querySelectorAll('.count-up');
    countUpElements.forEach(el => {
        const endValue = el.getAttribute('data-to');
        const isRupiah = el.innerText.includes('Rp');
        let instance;

        if (isRupiah) {
            instance = new CountUp(el, endValue, {
                prefix: 'Rp ',
                separator: '.',
                decimal: ',',
                duration: 2.5
            });
        } else {
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

            timelineContainer.style.display = 'none';
            timelineList.innerHTML = '';
            
            fetch(`/aset/${currentAsetId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detail-kode').textContent = data.kode;
                    document.getElementById('detail-kategori').textContent = data.nama_kategori;
                    document.getElementById('detail-sub-kategori').textContent = data.nama_sub_kategori;
                    document.getElementById('detail-merk').textContent = data.merk;
                    document.getElementById('detail-type').textContent = data.type || '-';
                    document.getElementById('detail-serial_number').textContent = data.serial_number || '-';
                    document.getElementById('detail-tahun').textContent = data.tahun;
                    document.getElementById('detail-harga_beli').textContent = formatRupiah(data.harga_beli);
                    document.getElementById('detail-entitas_pembelian').textContent = data.entitas_pembelian || '-';
                    document.getElementById('detail-lokasi').textContent = data.lokasi;
                    document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                    document.getElementById('detail-status').textContent = data.status;
                    document.getElementById('detail-updated_at').textContent = data.updated_at;
                })
                .catch(error => console.error('Error fetching detail:', error));
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

