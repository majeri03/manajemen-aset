<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    #qr-reader video { border-radius: 8px; }
    #qr-reader__dashboard_section_csr > div {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .scan-result-item {
        border-bottom: 1px solid #eee;
        padding: 10px 5px;
    }
    .scan-result-item.duplicate {
        background-color: #fff3cd; /* Warna kuning untuk duplikat */
    }
    .result-id { font-weight: 600; }
    .result-info { font-size: 0.85rem; color: #6c757d; }
</style>

<div class="main-header mb-4">
    <h4 class="mb-0">Scan Cepat Stock Opname</h4>
    <p class="text-muted small">Arahkan kamera ke beberapa QR Code secara bergantian untuk verifikasi cepat.</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-container shadow-sm">
            <h5 class="mb-3">Kamera Pemindai</h5>
            <div id="qr-reader" style="width:100%;"></div>
            <div class="d-grid gap-2 mt-3">
                <button id="startButton" class="btn btn-success"><i class="bi bi-camera-video-fill me-2"></i>Mulai Pindai</button>
                <button id="stopButton" class="btn btn-danger" style="display:none;"><i class="bi bi-stop-circle-fill me-2"></i>Hentikan Pindai</button>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-container shadow-sm">
            <h5>Hasil Pindaian (<span id="scan-count">0</span> Aset)</h5>
            <p class="text-muted small">Aset yang berhasil dipindai akan muncul di sini.</p>
            <form id="scan-form" action="<?= base_url('stockopname/process-scan') ?>" method="post">
                <?= csrf_field() ?>
                <div id="scan-results" class="mb-3" style="max-height: 400px; overflow-y: auto;">
                    </div>
                <button type="submit" class="btn btn-primary w-100" id="submit-button" disabled>
                    <i class="bi bi-check-all me-2"></i>Verifikasi Semua Aset
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. PENGATURAN AWAL ===
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    const resultsContainer = document.getElementById('scan-results');
    const scanCount = document.getElementById('scan-count');
    const submitButton = document.getElementById('submit-button');
    const scanForm = document.getElementById('scan-form');

    let scannedData = new Map();
    let isScanningPaused = false;
    const scanCooldown = 2000;
    const html5QrCode = new Html5Qrcode("qr-reader");

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    const beepSuccess = new Audio("<?= base_url('assets/sounds/beep-success.mp3') ?>");
    const beepError = new Audio("<?= base_url('assets/sounds/beep-error.mp3') ?>");

    // === 2. FUNGSI-FUNGSI BANTUAN ===
    function saveState() {
        sessionStorage.setItem('scannedAssets', JSON.stringify(Array.from(scannedData.entries())));
    }

    function loadState() {
        const storedData = sessionStorage.getItem('scannedAssets');
        if (storedData) {
            const parsedData = JSON.parse(storedData);
            scannedData = new Map(parsedData);
            redrawScannedList();
        }
    }

    function redrawScannedList() {
        resultsContainer.innerHTML = '';
        scanForm.querySelectorAll('input[name="asset_ids[]"]').forEach(input => input.remove());

        if (scannedData.size === 0) {
            scanCount.textContent = 0;
            submitButton.disabled = true;
            return;
        }

        scannedData.forEach((details, assetId) => {
            // [PERUBAHAN] Tambahkan kelas 'verified-today' jika aset sudah diverifikasi
            const verifiedClass = details.verified_today ? 'list-group-item-success' : '';
            const itemDiv = document.createElement('div');
            itemDiv.id = `item-${assetId}`;
            itemDiv.className = `scan-result-item list-group-item d-flex justify-content-between align-items-center ${verifiedClass}`;
            
            let infoText = 'Belum pernah diverifikasi.'; // Siapkan teks default
            if (details.opname_at) { // Cukup periksa apakah data tanggalnya ada atau tidak
                infoText = `Terakhir dicek: ${details.opname_at} oleh ${details.full_name}`;
            }
            
            // [PERUBAHAN] Tambahkan badge jika sudah diverifikasi
            let verifiedBadge = details.verified_today ? '<span class="badge bg-success ms-2">Terverifikasi Hari Ini</span>' : '';

            itemDiv.innerHTML = `
                <div>
                    <div class="result-id">${details.kode}${verifiedBadge}</div>
                    <div class="result-info">${infoText}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="${assetId}">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            resultsContainer.prepend(itemDiv);

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'asset_ids[]';
            hiddenInput.value = assetId;
            scanForm.appendChild(hiddenInput);
        });

        scanCount.textContent = scannedData.size;
        submitButton.disabled = false;
    }

    // === 3. FUNGSI UTAMA SAAT SCAN BERHASIL (Tidak ada perubahan di sini) ===
    const onScanSuccess = (decodedText, decodedResult) => {
        if (isScanningPaused) return;
        const match = decodedText.match(/aset\/(\d+)/);
        if (!match || !match[1]) return;
        const assetId = match[1];

        if (scannedData.has(assetId)) {
            beepError.play();
            Toast.fire({ icon: 'warning', title: 'Aset ini sudah ada di daftar!' });
            return;
        }

        isScanningPaused = true;

        fetch(`<?= base_url('api/last-verification/') ?>${assetId}`)
            .then(response => response.ok ? response.json() : Promise.reject(response))
            .then(data => {
                switch (data.status) {
                    case 'asset_not_found':
                        beepError.play();
                        Toast.fire({ icon: 'error', title: 'Error: Aset tidak ditemukan di database!' });
                        break;
                    case 'has_history':
                    case 'no_history':
                        beepSuccess.play();
                        Toast.fire({ icon: 'success', title: `Berhasil: ${data.data.kode} ditambahkan.` });
                        scannedData.set(assetId, data.data);
                        saveState();
                        redrawScannedList();
                        break;
                }
            })
            .catch(err => {
                beepError.play();
                Toast.fire({ icon: 'error', title: 'Gagal menghubungi server.' });
                console.error("Fetch error:", err);
            })
            .finally(() => {
                setTimeout(() => { isScanningPaused = false; }, scanCooldown);
            });
    };

    // === 4. EVENT LISTENERS (Tidak ada perubahan di sini) ===
    resultsContainer.addEventListener('click', function(e) {
        const deleteButton = e.target.closest('.delete-item');
        if (deleteButton) {
            const assetId = deleteButton.dataset.id;
            scannedData.delete(assetId);
            saveState();
            redrawScannedList();
            Toast.fire({ icon: 'info', title: 'Item dihapus dari daftar.' });
        }
    });

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    startButton.addEventListener('click', () => {
        beepSuccess.play().catch(()=>{}); beepSuccess.pause(); beepSuccess.currentTime = 0;
        beepError.play().catch(()=>{}); beepError.pause(); beepError.currentTime = 0;
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => {
                startButton.style.display = 'none';
                stopButton.style.display = 'block';
            }).catch(err => Swal.fire('Error', 'Gagal mengakses kamera: ' + err, 'error'));
    });
    stopButton.addEventListener('click', () => {
        html5QrCode.stop().then(() => {
            startButton.style.display = 'block';
            stopButton.style.display = 'none';
        }).catch(err => console.error("Gagal menghentikan scanner.", err));
    });

    scanForm.addEventListener('submit', function() {
        sessionStorage.removeItem('scannedAssets');
    });

    loadState();
});
</script>
<?= $this->endSection() ?>