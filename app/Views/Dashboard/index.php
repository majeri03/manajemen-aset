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
                <input type="text" class="form-control" placeholder="Cari aset...">
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
                    <h4 class="count-up" data-to="1250000000">110</h4>
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
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="card-text">
                    <h6 class="text-muted">Total Pengguna</h6>
                    <h4 class="count-up" data-to="42">0 Orang</h4>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <select class="form-select me-2">
                            <option>Semua Kategori</option>
                            <option>Elektronik</option>
                            <option>Mebel</option>
                            <option>Kendaraan</option>
                        </select>
                        <select class="form-select me-2">
                            <option>Semua Lokasi</option>
                            <option>Lantai 12</option>
                            <option>Ruang Meeting A</option>
                            <option>Gudang</option>
                        </select>
                        <select class="form-select">
                            <option>Semua Status</option>
                            <option>Aktif</option>
                            <option>Rusak</option>
                            <option>Dijual</option>
                        </select>
                    </div>
                </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BTR/02/01/2021/EPS</td>
                                <td>Printer</td>
                                <td>Epson</td>
                                <td>XBN4503766</td>
                                <td>2021</td>
                                <td>Head Office - Rg. HCGA</td>
                                <td>SO 2024</td>
                            </tr>
                            <tr>
                                <td>BTR/02/04/2023/PST</td>
                                <td>Printer Label</td>
                                <td>Postek</td>
                                <td>C4823G0219</td>
                                <td>2023</td>
                                <td>Head Office - Rg. HCGA</td>
                                <td>SO 2024</td>
                            </tr>
                            <tr>
                                <td>BTR/02/01/2019/SHP</td>
                                <td>TV</td>
                                <td>Sharp</td>
                                <td></td>
                                <td>2019</td>
                                <td>Head Office - Rg. HCGA</td>
                                <td>SO 2024</td>
                            </tr>
                            <tr>
                                <td>BTR/02/04/2020/HP</td>
                                <td>Printer</td>
                                <td>HP</td>
                                <td>VNC5M95560</td>
                                <td>2020</td>
                                <td>Head Office - Rg. Finance Acc</td>
                                <td>SO 2025</td>
                            </tr>
                            <tr>
                                <td>BTR/02/04/2020/EPS.05</td>
                                <td>Printer</td>
                                <td>epson</td>
                                <td>X5DK2X234</td>
                                <td>2020</td>
                                <td>Tidak terpakai</td>
                                <td>SO 2024</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Aset</label>
                        <input type="text" class="form-control" id="kode" name="kode" placeholder="Misalnya: BTR/02/01/2021/EPS" 
                               oninput="this.value = this.value.toUpperCase()"> </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori Barang</label>
                        <select class="form-select" id="kategori" name="kategori">
                            <option selected>Pilih Kategori</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Perabotan">Perabotan</option>
                            <option value="Kendaraan">Kendaraan</option>
                            <option value="Mutasi Aset">Mutasi Aset</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="merk" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" placeholder="Misalnya: Epson" 
                               oninput="this.value = this.value.toUpperCase()"> </div>
                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Misalnya: XBN4503766" 
                               oninput="this.value = this.value.toUpperCase()"> </div>
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Misalnya: 2021">
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <select class="form-select" id="lokasi" name="lokasi">
                            <option selected>Pilih Lokasi</option>
                            <option value="Head Office - Rg. HCGA">Head Office - Rg. HCGA</option>
                            <option value="Head Office - Rg. Finance Acc">Head Office - Rg. Finance Acc</option>
                            <option value="Lantai 8 - Bussiness Dev">Lantai 8 - Bussiness Dev</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                  oninput="this.value = this.value.toUpperCase()"></textarea> </div>
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
    // Inisialisasi Chart.js
    var ctx1 = document.getElementById('assetCategoryChart').getContext('2d');
    var assetCategoryChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['Elektronik', 'Mebel', 'Kendaraan', 'Perlengkapan Kantor'],
            datasets: [{
                label: 'Jumlah Aset',
                data: [300, 150, 50, 120],
                backgroundColor: [
                    '#003481',
                    '#3da2ff',
                    '#ffc107',
                    '#6c757d',
                ],
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

    var ctx2 = document.getElementById('assetStatusChart').getContext('2d');
    var assetStatusChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Aktif', 'Rusak', 'Dalam Perbaikan', 'Dijual'],
            datasets: [{
                label: 'Jumlah Aset',
                data: [500, 15, 5, 2],
                backgroundColor: [
                    '#065f46',
                    '#991b1b',
                    '#92400e',
                    '#003481',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                }
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
                    suffix: ' ' + el.innerText.split(' ')[1],
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
</script>
<?= $this->endSection() ?>