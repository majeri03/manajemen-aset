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
            <button class="btn btn-custom-icon"><i class="bi bi-geo-alt me-2"></i> Tracking Aset</button>
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
                <form action="<?= base_url('aset') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_to" value="aset">
                    <input type="hidden" name="redirect_to" value="dashboard">

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori Barang</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" placeholder="Contoh: PRINTER" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>

                    <div class="mb-3">
                        <label for="merk" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" placeholder="Contoh: EPSON" oninput="this.value = this.value.toUpperCase(); generateKodeAset();" required>
                    </div>

                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Contoh: XBN4503766" oninput="this.value = this.value.toUpperCase();">
                    </div>

                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Contoh: 2025" oninput="generateKodeAset();" required>
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
                        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Contoh: HEAD OFFICE - RG. HCGA" oninput="this.value = this.value.toUpperCase();">
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

    function exportLaporanBulanan(bulan) {
        // Arahkan browser ke URL ekspor dengan bulan yang dipilih
        window.location.href = `<?= base_url('dashboard/export/') ?>${bulan}`;
    }

    // GENERATE KODE ASET OTOMATIS
    function generateKodeAset() {
        const kategori = document.getElementById('kategori').value.toUpperCase().replace(/\s+/g, '').substring(0, 5);
        const tahun = document.getElementById('tahun').value;
        const merk = document.getElementById('merk').value.toUpperCase().replace(/\s+/g, '').substring(0, 3);
        
        if (kategori && tahun && merk) {
            const kodeAset = `BTR/${kategori}/${tahun}/${merk}`;
            document.getElementById('kode').value = kodeAset;
        } else {
            document.getElementById('kode').value = '';
        }
    }



    // Inisialisasi Chart.js
    
    // --- PIE CHART DINAMIS ---
    
    // 1. Ambil data dari controller PHP
    const labels = <?= json_encode($chartLabels) ?>;
    const data = <?= json_encode($chartData) ?>;

    // 2. Fungsi untuk menghasilkan warna acak
    function generateRandomColor() {
        const r = Math.floor(Math.random() * 200);
        const g = Math.floor(Math.random() * 200);
        const b = Math.floor(Math.random() * 200);
        return `rgb(${r}, ${g}, ${b})`;
    }

    // 3. Buat array warna dinamis sejumlah data yang ada
    const dynamicColors = labels.map(() => generateRandomColor());

    // 4. Inisialisasi Chart.js dengan data dinamis
    var ctx1 = document.getElementById('assetCategoryChart').getContext('2d');
    var assetCategoryChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: labels, // Gunakan data label dari controller
            datasets: [{
                label: 'Jumlah Aset',
                data: data, // Gunakan data jumlah dari controller
                backgroundColor: dynamicColors, // Gunakan warna yang digenerate otomatis
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


    // --- FUNGSI DETAIL ASET (MODAL) ---
const detailAsetModal = document.getElementById('detailAsetModal');
if (detailAsetModal) {
    detailAsetModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const asetId = button.getAttribute('data-id');
        
        // Sesuaikan URL fetch dengan resource route
        fetch(`/aset/${asetId}`) 
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

// Ganti kode JavaScript untuk assetStatusChart dengan ini:

// 1. Ambil data status dari controller
const statusLabels = <?= json_encode($statusLabels) ?>;
const statusData = <?= json_encode($statusData) ?>;

// 2. Siapkan warna (Anda bisa menambahkannya jika statusnya lebih banyak)
const statusColors = {
    'BAIK': '#065f46', // Hijau
    'RUSAK': '#991b1b', // Merah
    'TIDAK TERPAKAI': '#92400e' // Oranye tua
    // Tambahkan warna lain di sini jika ada status baru, misal: 'DALAM PERBAIKAN': '#...'
};

// 3. Buat array warna dinamis berdasarkan label status
const dynamicStatusColors = statusLabels.map(label => statusColors[label.toUpperCase()] || '#6c757d'); // Abu-abu jika status tidak dikenali

// 4. Inisialisasi Chart.js dengan data dinamis
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



    // Inisialisasi CountUp.js
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
        const detailAsetModal = document.getElementById('detailAsetModal');
        
        detailAsetModal.addEventListener('show.bs.modal', function(event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;
            // Ambil ID dari atribut data-*
            const asetId = button.getAttribute('data-id');

            // Ambil data dari server menggunakan Fetch API
            fetch(`/aset/detail/${asetId}`)
                .then(response => response.json())
                .then(data => {
                    // Isi konten modal dengan data yang diterima
                    document.getElementById('detail-kode').textContent = data.kode;
                    document.getElementById('detail-kategori').textContent = data.kategori;
                    document.getElementById('detail-merk').textContent = data.merk;
                    document.getElementById('detail-serial_number').textContent = data.serial_number || '-';
                    document.getElementById('detail-tahun').textContent = data.tahun;
                    document.getElementById('detail-lokasi').textContent = data.lokasi;
                    document.getElementById('detail-keterangan').textContent = data.keterangan || '-';
                    document.getElementById('detail-updated_at').textContent = data.updated_at;
                })
                .catch(error => console.error('Error:', error));
        });
    });


    // --- LOGIKA UNTUK MENAMPILKAN POPUP QR CODE ---
    <?php if (session()->getFlashdata('new_aset')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data aset dari session flash
            const newAset = <?= json_encode(session()->getFlashdata('new_aset')) ?>;
            
            // Isi konten modal dengan data
            document.getElementById('qr-kode').textContent = newAset.kode;
            document.getElementById('qr-detail').textContent = `${newAset.kategori} - ${newAset.merk}`;
            document.getElementById('qr-image').src = `<?= base_url() ?>/${newAset.qrcode}`;
            
            // Tampilkan modal
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
        // Kita perlu me-reload agar event listener dan fungsionalitas lain kembali normal
        window.location.reload(); 
    }

    

</script>
<?= $this->endSection() ?>