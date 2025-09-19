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
            <div class="input-group search-bar me-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Cari aset...">
                <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
            </div>
            <button class="btn btn-custom-icon me-2" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                <i class="bi bi-plus-circle me-2"></i> Tambah Aset
            </button>
            <button class="btn btn-custom-icon me-2"><i class="bi bi-file-earmark-arrow-up me-2"></i> Export Laporan</button>
            <button class="btn btn-custom-icon"><i class="bi bi-geo-alt me-2"></i> Tracking Aset</button>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="summary-card shadow-sm">
                <div class="card-icon total-aset">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="card-text">
                    <h6 class="text-muted">Total Jumlah Aset</h6>
                    <h4 class="count-up" data-to="110">110</h4>
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
                    <h4 class="count-up" data-to="15">0 Unit</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="summary-card shadow-sm">
                <div class="card-icon pengguna">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <div class="card-text">
                    <h6 class="text-muted">Permintaan Perubahan</h6>
                    <h4 class="count-up" data-to="<?= $pending_requests ?>"><?= $pending_requests ?> Permintaan</h4>
                </div>
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

    <div class="row mt-5">
        <div class="col-12">
            <div class="table-container shadow-sm">
                <h5 class="mb-4">Aset Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">KODE</th>
                                <th scope="col">KATEGORI</th>
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
                                        <td><?= esc($aset['kategori']) ?></td>
                                        <td><?= esc($aset['merk']) ?></td>
                                        <td><?= esc($aset['serial_number']) ?></td>
                                        <td><?= esc($aset['tahun']) ?></td>
                                        <td><?= esc($aset['lokasi']) ?></td>
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
                                    <td colspan="8" class="text-center">Belum ada data aset.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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

<div class="modal fade" id="tambahAsetModal" tabindex="-1" aria-labelledby="tambahAsetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahAsetModalLabel">Tambah Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/dashboard/tambah-aset" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori Barang</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" placeholder="Misalnya: PRINTER" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="merk" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" placeholder="Misalnya: EPSON" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Misalnya: XBN4503766" oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Misalnya: 2025" oninput="generateKodeAset();" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Aset</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Baik" selected>Baik</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Tidak terpakai">Tidak terpakai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Misalnya: HEAD OFFICE - RG. HCGA" oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" oninput="this.value = this.value.toUpperCase();"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Aset (Otomatis)</label>
                        <input type="text" class="form-control" id="kode" name="kode" readonly style="background-color: #e9ecef;">
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
<?= $this->endSection() ?>


<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.7/dist/countUp.min.js"></script>
<script>
    // --- FUNGSI GLOBAL ---
    function generateKodeAset() {
        const kategori = document.getElementById('kategori').value.toUpperCase().replace(/\s+/g, '').substring(0, 5);
        const tahun = document.getElementById('tahun').value;
        const merk = document.getElementById('merk').value.toUpperCase().replace(/\s+/g, '').substring(0, 3);
        
        if (kategori && tahun && merk) {
            document.getElementById('kode').value = `BTR/${kategori}/${tahun}/${merk}`;
        } else {
            document.getElementById('kode').value = '';
        }
    }

    // --- INISIALISASI SETELAH SEMUA ELEMEN HTML SIAP ---
    document.addEventListener('DOMContentLoaded', function() {

        // Inisialisasi CountUp.js
        const countUpElements = document.querySelectorAll('.count-up');
        countUpElements.forEach(el => {
            const endValue = el.getAttribute('data-to');
            if (typeof CountUp !== 'undefined') {
                const instance = new CountUp(el, endValue, { duration: 2.5 });
                if (!instance.error) {
                    instance.start();
                } else {
                    console.error(instance.error);
                }
            }
        });

        // --- PIE CHART DINAMIS ---
        const labels = <?= json_encode($chartLabels) ?>;
        const data = <?= json_encode($chartData) ?>;
        const assetCategoryChartCanvas = document.getElementById('assetCategoryChart');
        if (assetCategoryChartCanvas) {
            const dynamicColors = labels.map(() => `rgb(${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)}, ${Math.floor(Math.random() * 200)})`);
            const ctx1 = assetCategoryChartCanvas.getContext('2d');
            new Chart(ctx1, {
                type: 'pie',
                data: { labels: labels, datasets: [{ label: 'Jumlah Aset', data: data, backgroundColor: dynamicColors, hoverOffset: 4 }] },
                options: { responsive: true, plugins: { legend: { position: 'bottom' }, title: { display: false } } }
            });
        }

        // --- BAR CHART STATUS DINAMIS ---
        const statusLabels = <?= json_encode($statusLabels) ?>;
        const statusData = <?= json_encode($statusData) ?>;
        const assetStatusChartCanvas = document.getElementById('assetStatusChart');
        if (assetStatusChartCanvas) {
            const statusColors = { 'BAIK': '#065f46', 'RUSAK': '#991b1b', 'TIDAK TERPAKAI': '#92400e' };
            const dynamicStatusColors = statusLabels.map(label => statusColors[label.toUpperCase()] || '#6c757d');
            const ctx2 = assetStatusChartCanvas.getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: { labels: statusLabels, datasets: [{ label: 'Jumlah Aset', data: statusData, backgroundColor: dynamicStatusColors, borderWidth: 1 }] },
                options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false }, title: { display: false } } }
            });
        }

        // --- FUNGSI DETAIL ASET (MODAL) ---
        const detailAsetModal = document.getElementById('detailAsetModal');
        if (detailAsetModal) {
            detailAsetModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const asetId = button.getAttribute('data-id');
                fetch(`/aset/detail/${asetId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('detail-kode').textContent = data.kode;
                        document.getElementById('detail-kategori').textContent = data.kategori;
                        document.getElementById('detail-merk').textContent = data.merk;
                        document.getElementById('detail-serial_number').textContent = data.serial_number || '-';
                        document.getElementById('detail-tahun').textContent = data.tahun;
                        document.getElementById('detail-lokasi').textContent = data.lokasi;
                        document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                        document.getElementById('detail-updated_at').textContent = data.updated_at;
                    })
                    .catch(error => console.error('Error fetching detail:', error));
            });
        }

        // --- FUNGSI PENCARIAN ASET ---
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('asetTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function() {
                const keyword = this.value;
                fetch(`/aset/search?q=${keyword}`)
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(aset => {
                                const row = `<tr><td>${aset.kode}</td><td>${aset.kategori}</td><td>${aset.merk}</td><td>${aset.serial_number || '-'}</td><td>${aset.tahun}</td><td>${aset.lokasi}</td><td>${aset.keterangan || '-'}</td><td><button type="button" class="btn btn-info btn-sm view-detail" data-bs-toggle="modal" data-bs-target="#detailAsetModal" data-id="${aset.id}"><i class="bi bi-eye-fill"></i></button></td></tr>`;
                                tableBody.innerHTML += row;
                            });
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="8" class="text-center">Aset tidak ditemukan.</td></tr>`;
                        }
                    })
                    .catch(error => console.error('Error searching:', error));
            });
        }
    });
</script>
<?= $this->endSection() ?>