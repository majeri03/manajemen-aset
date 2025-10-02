<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .list-group-item.scanned-found { background-color: #d1e7dd; } /* Hijau untuk yang ditemukan */
    .list-group-item.scanned-misplaced { background-color: #fff3cd; } /* Kuning untuk yang salah lokasi */
    .list-group-item.verified-today { border-left: 5px solid #198754; } /* Garis hijau tebal */
    .list-group-item .asset-code { font-weight: 600; }
    .list-group-item .asset-info { font-size: 0.85rem; color: #6c757d; }
    .accordion-button:not(.collapsed) { background-color: #e7f1ff; }
</style>

<div class="main-header mb-4">
    <h4 class="mb-0">Scan Cepat Stock Opname</h4>
    <p class="text-muted small">Pilih lokasi audit untuk memulai sesi pemindaian.</p>
</div>

<div class="table-container shadow-sm mb-4">
    <label for="lokasi-select" class="form-label fw-bold">Pilih Lokasi Audit</label>
    <select id="lokasi-select" class="form-select form-select-lg">
        <option value="">-- Silakan Pilih Lokasi --</option>
        <?php foreach($lokasi_list as $lokasi): ?>
            <option value="<?= $lokasi['id'] ?>"><?= esc($lokasi['nama_lokasi']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div id="scan-container" class="row g-4" style="display: none;">

    <div class="col-lg-5 order-lg-2">
        <div class="table-container shadow-sm">
            <h5 class="mb-3">Kamera Pemindai</h5>
            <div id="qr-reader" style="width:100%;"></div>
            <div class="d-grid gap-2 mt-3">
                <button id="startButton" class="btn btn-success"><i class="bi bi-camera-video-fill me-2"></i>Mulai Pindai</button>
                <button id="stopButton" class="btn btn-danger" style="display:none;"><i class="bi bi-stop-circle-fill me-2"></i>Hentikan Pindai</button>
            </div>
            <hr>
            <form id="scan-form" action="<?= base_url('stockopname/process-scan') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary w-100" id="submit-button" disabled>
                    <i class="bi bi-check-all me-2"></i>Verifikasi Aset yang Ditemukan
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7 order-lg-1">
        <div class="table-container shadow-sm">
            <h5 class="mb-3">Checklist Aset</h5>
            <div class="accordion" id="checklistAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTunggu">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTunggu" aria-expanded="true" aria-controls="collapseTunggu">
                            Daftar Tunggu (<span id="tunggu-count">0</span>)
                        </button>
                    </h2>
                    <div id="collapseTunggu" class="accordion-collapse collapse show" aria-labelledby="headingTunggu">
                        <div id="tunggu-list" class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;"></div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDitemukan">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDitemukan" aria-expanded="false" aria-controls="collapseDitemukan">
                            Sudah Ditemukan (<span id="ditemukan-count">0</span>)
                        </button>
                    </h2>
                    <div id="collapseDitemukan" class="accordion-collapse collapse" aria-labelledby="headingDitemukan">
                        <div id="ditemukan-list" class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. PENGATURAN ELEMEN & VARIABEL ===
    const lokasiSelect = document.getElementById('lokasi-select');
    const scanContainer = document.getElementById('scan-container');
    const tungguList = document.getElementById('tunggu-list');
    const ditemukanList = document.getElementById('ditemukan-list');
    const tungguCount = document.getElementById('tunggu-count');
    const ditemukanCount = document.getElementById('ditemukan-count');
    const scanForm = document.getElementById('scan-form');
    const submitButton = document.getElementById('submit-button');
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');

    let allAssetsInLocation = new Map();
    let foundAssets = new Map();

    const html5QrCode = new Html5Qrcode("qr-reader");
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    const beepSuccess = new Audio("<?= base_url('assets/sounds/beep-success.mp3') ?>");
    const beepError = new Audio("<?= base_url('assets/sounds/beep-error.mp3') ?>");

    // === 2. FUNGSI-FUNGSI BANTUAN ===
    function saveState() {
        try {
            const state = {
                locationId: lokasiSelect.value,
                found: Array.from(foundAssets.entries())
            };
            sessionStorage.setItem('scanSession', JSON.stringify(state));
        } catch (e) {
            console.error("Gagal menyimpan state:", e);
        }
    }

    function loadState() {
        const storedState = sessionStorage.getItem('scanSession');
        if (storedState) {
            try {
                const state = JSON.parse(storedState);
                if (state.locationId) {
                    lokasiSelect.value = state.locationId;
                    foundAssets = new Map(state.found);
                    lokasiSelect.dispatchEvent(new Event('change'));
                }
            } catch (e) {
                console.error("Gagal memuat state:", e);
                sessionStorage.removeItem('scanSession');
            }
        }
    }

    function redrawLists() {
    tungguList.innerHTML = '';
    ditemukanList.innerHTML = '';
    // Hapus hanya hidden input aset, bukan csrf token
    scanForm.querySelectorAll('input[name="asset_ids[]"]').forEach(el => el.remove());

    let inPlaceFoundCount = 0;

    allAssetsInLocation.forEach((asset, assetIdStr) => {
        if (!foundAssets.has(assetIdStr)) {
            const item = document.createElement('div');
            item.id = `asset-${assetIdStr}`;
            item.className = 'list-group-item';
            item.innerHTML = `<div class="asset-code">${asset.kode}</div>`;
            tungguList.appendChild(item);
        }
    });

    foundAssets.forEach((details, assetIdStr) => {
        const item = document.createElement('div');
        item.id = `found-item-${assetIdStr}`;
        item.className = 'list-group-item d-flex justify-content-between align-items-center';
        
        if (!details.is_misplaced) {
            item.classList.add('scanned-found');
            inPlaceFoundCount++;
        } else {
            item.classList.add('scanned-misplaced');
        }
        if(details.status_verifikasi === 'Sudah Dicek') { 
            item.classList.add('verified-today');
        }

        let infoText = details.is_misplaced 
            ? `<span class="text-danger">Lokasi Salah! Seharusnya di: ${details.actual_location}</span>` 
            : 'Lokasi Sesuai';

        let verifiedBadge = details.status_verifikasi === 'Sudah Dicek' ? '<span class="badge bg-success ms-2">Sudah Diverifikasi</span>' : ''; 

        item.innerHTML = `
            <div>
                <div class="asset-code">${details.kode}${verifiedBadge}</div>
                <small class="asset-info">${infoText}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="${assetIdStr}">
                <i class="bi bi-trash" style="pointer-events: none;"></i>
            </button>
        `;
        ditemukanList.prepend(item);

        // =================================================================
        // PERUBAHAN LOGIKA UTAMA ADA DI SINI
        // Hanya tambahkan aset yang lokasinya sesuai ke dalam form
        // =================================================================
        if (!details.is_misplaced) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'asset_ids[]';
            hiddenInput.value = assetIdStr;
            scanForm.appendChild(hiddenInput);
        }
    });
    
    tungguCount.textContent = allAssetsInLocation.size - inPlaceFoundCount;
    ditemukanCount.textContent = foundAssets.size;
    
    // Tombol submit hanya aktif jika ada setidaknya satu aset yang lokasinya benar
    submitButton.disabled = scanForm.querySelectorAll('input[name="asset_ids[]"]').length === 0;
}

    // === 3. LOGIKA UTAMA ===
    lokasiSelect.addEventListener('change', function(e) {
        const locationId = this.value;
        allAssetsInLocation.clear(); 

        // [PERBAIKAN] Hanya jalankan clear jika event dipicu oleh interaksi pengguna langsung
        if (e.isTrusted) {
            foundAssets.clear(); 
            sessionStorage.removeItem('scanSession'); // Hapus cache lama jika user ganti lokasi
        }

        if (locationId) {
            scanContainer.style.display = 'flex';
            fetch(`<?= base_url('api/aset-by-location/') ?>${locationId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(asset => allAssetsInLocation.set(String(asset.id), asset));
                    redrawLists(); // Gambar ulang daftar berdasarkan data yang mungkin ada di cache
                    if (e.isTrusted) {
                       saveState(); // Simpan state baru hanya jika lokasi diubah manual
                    }
                });
        } else {
            scanContainer.style.display = 'none';
        }
    });

    let isScanningPaused = false;
    const onScanSuccess = (decodedText, decodedResult) => {
        if (isScanningPaused) return;

        const match = decodedText.match(/aset\/(\d+)/);
        if (!match || !match[1]) return;
        const assetId = match[1];

        if (foundAssets.has(assetId)) {
            beepError.play(); Toast.fire({ icon: 'warning', title: 'Aset ini sudah ada di daftar!' }); return;
        }

        isScanningPaused = true;
        
        fetch(`<?= base_url('api/last-verification/') ?>${assetId}`)
            .then(response => response.ok ? response.json() : Promise.reject(response))
            .then(data => {
                if (data.status === 'asset_not_found') {
                    beepError.play(); Toast.fire({ icon: 'error', title: 'Aset tidak ada di database!' }); return;
                }
                beepSuccess.play();
                Toast.fire({ icon: 'success', title: `Berhasil: ${data.data.kode} ditambahkan.` });
                let assetDetails = data.data;
                assetDetails.is_misplaced = !allAssetsInLocation.has(assetId);
                assetDetails.actual_location = data.data.nama_lokasi;
                foundAssets.set(assetId, assetDetails);
                redrawLists();
                saveState();
            })
            .catch(err => console.error("Fetch error:", err))
            .finally(() => setTimeout(() => { isScanningPaused = false; }, 2000));
    };

    ditemukanList.addEventListener('click', function(e) {
        if (e.target.closest('.delete-item')) {
            const assetId = e.target.closest('.delete-item').dataset.id;
            foundAssets.delete(assetId);
            redrawLists();
            saveState();
            Toast.fire({ icon: 'info', title: 'Item dihapus dari daftar.' });
        }
    });

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    startButton.addEventListener('click', () => {
        beepSuccess.play().catch(()=>{}); beepSuccess.pause(); beepSuccess.currentTime = 0;
        beepError.play().catch(()=>{}); beepError.pause(); beepError.currentTime = 0;
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => { startButton.style.display = 'none'; stopButton.style.display = 'block'; })
            .catch(err => Swal.fire('Error', 'Gagal mengakses kamera: ' + err, 'error'));
    });
    stopButton.addEventListener('click', () => {
        html5QrCode.stop().then(() => { startButton.style.display = 'block'; stopButton.style.display = 'none'; });
    });

    scanForm.addEventListener('submit', () => sessionStorage.removeItem('scanSession'));
    loadState();
});
</script>
<?= $this->endSection() ?>