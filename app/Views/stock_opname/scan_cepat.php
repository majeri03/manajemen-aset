<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Styling tidak berubah */
    .list-group-item.scanned-found { background-color: #d1e7dd; }
    .list-group-item.scanned-misplaced { background-color: #fff3cd; }
    .list-group-item.verified-today { border-left: 5px solid #198754; }
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
            <button type="button" class="btn btn-primary w-100" id="finish-session-button" disabled>
                <i class="bi bi-check-all me-2"></i>Cros cek & Finalisasi
            </button>
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
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Verifikasi Aset: <span id="modal-asset-code"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modal-form">
                    <input type="hidden" id="modal-asset-id" name="asset_id">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5>Data Aset Saat Ini</h5>
                            <p class="mb-2"><strong>Sub Kategori:</strong> <span id="modal-sub-kategori"></span></p>
                            <p class="mb-2"><strong>Merk:</strong> <span id="modal-merk"></span></p>
                            <p class="mb-2"><strong>Tipe:</strong> <span id="modal-tipe"></span></p>
                            <p class="mb-2"><strong>Serial Number:</strong> <span id="modal-sn"></span></p>
                            <hr>
                            <p class="mb-2"><strong>Lokasi Terdaftar:</strong> <span id="modal-lokasi-terakhir"></span></p>
                            <p class="mb-2"><strong>Status Terakhir:</strong> <span id="modal-status-terakhir"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Verifikasi & Usulan Perubahan</h5>
                            <div class="alert alert-warning" id="modal-misplaced-alert" style="display: none;">
                                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Lokasi Salah!</strong> Aset ini terdaftar di lokasi lain.
                            </div>
                            <div class="mb-3">
                                <label for="modal-lokasi-id" class="form-label">Lokasi Saat Ini</label>
                                <select class="form-select" id="modal-lokasi-id" name="lokasi_id" required>
                                    <?php foreach ($lokasi_list as $lokasi): ?>
                                        <option value="<?= $lokasi['id'] ?>"><?= esc($lokasi['nama_lokasi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="modal-status" class="form-label">Kondisi Aset</label>
                                <select class="form-select" id="modal-status" name="status" required>
                                    <option value="Baik Terpakai">Baik (Terpakai)</option>
                                    <option value="Baik Tidak Terpakai">Baik (Tidak Terpakai)</option>
                                    <option value="Rusak">Rusak</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="modal-keterangan" class="form-label">Catatan</label>
                                <textarea class="form-control" id="modal-keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="modal-save-button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<form id="report-form" action="<?= base_url('stockopname/session-report') ?>" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="scan_session_data" id="scan_session_data">
</form>


<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === 1. PENGATURAN ELEMEN & VARIABEL ===
    const lokasiSelect = document.getElementById('lokasi-select');
    const scanContainer = document.getElementById('scan-container');
    const tungguList = document.getElementById('tunggu-list');
    const tungguCount = document.getElementById('tunggu-count');
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    const finishButton = document.getElementById('finish-session-button');
    
    let allAssetsInLocation = new Map();
    let finalizedAssets = new Map();

    const html5QrCode = new Html5Qrcode("qr-reader");
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    const beepSuccess = new Audio("<?= base_url('assets/sounds/beep-success.mp3') ?>");
    const beepError = new Audio("<?= base_url('assets/sounds/beep-error.mp3') ?>");
    const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));

    // === 2. FUNGSI-FUNGSI BANTUAN (Tidak berubah) ===
    function redrawTungguList() {
        tungguList.innerHTML = '';
        let count = 0;
        allAssetsInLocation.forEach((asset, assetIdStr) => {
            if (!finalizedAssets.has(assetIdStr)) {
                const item = document.createElement('div');
                item.id = `asset-${assetIdStr}`;
                item.className = 'list-group-item';
                item.innerHTML = `<div class="asset-code">${asset.kode}</div>`;
                tungguList.appendChild(item);
                count++;
            }
        });
        tungguCount.textContent = count;
        finishButton.disabled = finalizedAssets.size === 0;
    }

    // === 3. LOGIKA UTAMA ===
    lokasiSelect.addEventListener('change', function(e) {
        const locationId = this.value;
        allAssetsInLocation.clear();
        finalizedAssets.clear(); 

        if (locationId) {
            scanContainer.style.display = 'flex';
            fetch(`<?= base_url('api/aset-by-location/') ?>${locationId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(asset => allAssetsInLocation.set(String(asset.id), asset));
                    redrawTungguList();
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

        if (finalizedAssets.has(assetId)) {
            beepError.play();
            Toast.fire({ icon: 'warning', title: 'Aset ini sudah difinalisasi!' });
            return;
        }

        isScanningPaused = true;
        
        fetch(`<?= base_url('aset/') ?>${assetId}`)
            .then(response => response.ok ? response.json() : Promise.reject(new Error('Aset tidak ditemukan')))
            .then(asset => {
                document.getElementById('modal-asset-id').value = assetId;
                document.getElementById('modal-asset-code').textContent = asset.kode;
                document.getElementById('modal-sub-kategori').textContent = asset.nama_sub_kategori || '-';
                document.getElementById('modal-merk').textContent = asset.nama_merk || '-';
                document.getElementById('modal-tipe').textContent = asset.nama_tipe || '-';
                document.getElementById('modal-sn').textContent = asset.serial_number || '-';
                document.getElementById('modal-lokasi-terakhir').textContent = asset.nama_lokasi || '-';
                document.getElementById('modal-status-terakhir').textContent = asset.status || '-';
                
                document.getElementById('modal-lokasi-id').value = lokasiSelect.value;
                document.getElementById('modal-status').value = asset.status;
                document.getElementById('modal-keterangan').value = asset.keterangan || '';
                
                const isMisplaced = (asset.lokasi_id != lokasiSelect.value);
                document.getElementById('modal-misplaced-alert').style.display = isMisplaced ? 'block' : 'none';

                beepSuccess.play();
                verificationModal.show();
            })
            .catch(err => {
                console.error("Fetch error:", err.message);
                beepError.play();
                Toast.fire({ icon: 'error', title: 'Gagal: ' + err.message });
                isScanningPaused = false;
            });
    };

    document.getElementById('modal-save-button').addEventListener('click', function() {
        const assetId = document.getElementById('modal-asset-id').value;
        const isMisplaced = document.getElementById('modal-misplaced-alert').style.display === 'block';

        const assetData = {
            id: assetId,
            kode: document.getElementById('modal-asset-code').textContent,
            is_misplaced: isMisplaced,
            actual_location: document.getElementById('modal-lokasi-terakhir').textContent, // [PENTING] Simpan lokasi asli
            lokasi_id: document.getElementById('modal-lokasi-id').value,
            status: document.getElementById('modal-status').value,
            keterangan: document.getElementById('modal-keterangan').value,
        };

        finalizedAssets.set(assetId, assetData);
        redrawTungguList();
        verificationModal.hide();
        Toast.fire({ icon: 'success', title: 'Aset difinalisasi!' });

        if (parseInt(tungguCount.textContent) === 0 && html5QrCode.getState() === 2) { // state 2 berarti kamera sedang aktif
            html5QrCode.stop().then(() => {
                startButton.style.display = 'block';
                stopButton.style.display = 'none';
                Swal.fire({
                    icon: 'success',
                    title: 'Selesai!',
                    text: 'Semua aset di lokasi ini telah berhasil dipindai.',
                    confirmButtonText: 'OK'
                });
            }).catch(err => console.log("Gagal menghentikan kamera secara otomatis."));
        }
    });

    // [MODIFIKASI UTAMA] Event listener untuk tombol Selesaikan Sesi
    finishButton.addEventListener('click', function() {
        // Mengubah Map menjadi objek biasa agar bisa di-JSON-kan
        const dataToSend = {
            lokasi_id: lokasiSelect.value,
            lokasi_nama: lokasiSelect.options[lokasiSelect.selectedIndex].text,
            all_assets: Object.fromEntries(allAssetsInLocation),
            finalized_assets: Object.fromEntries(finalizedAssets)
        };

        // Masukkan data ke form tersembunyi dan submit
        document.getElementById('scan_session_data').value = JSON.stringify(dataToSend);
        document.getElementById('report-form').submit();
    });

    document.getElementById('verificationModal').addEventListener('hidden.bs.modal', () => {
        isScanningPaused = false;
    });

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    startButton.addEventListener('click', () => {
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .then(() => { 
                startButton.style.display = 'none'; 
                stopButton.style.display = 'block'; 
            })
            .catch(err => Swal.fire('Error', 'Gagal mengakses kamera: ' + err, 'error'));
    });

    stopButton.addEventListener('click', () => {
        html5QrCode.stop().then(() => { 
            startButton.style.display = 'block'; 
            stopButton.style.display = 'none'; 
        }).catch(err => console.log("Gagal menghentikan kamera."));
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editReportModalEl = document.getElementById('editReportModal');
    const editReportModal = new bootstrap.Modal(editReportModalEl);
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

    // Event saat modal edit akan ditampilkan
    editReportModalEl.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const assetId = button.getAttribute('data-asset-id');
        const type = button.getAttribute('data-type');
        const row = document.getElementById(`row-${type}-${assetId}`);

        const kode = row.querySelector('.kode-text').textContent;
        const status = row.querySelector('.status-text').textContent;
        const keterangan = row.querySelector('.keterangan-text').textContent;

        // Isi form di dalam modal
        document.getElementById('edit-modal-asset-id').value = assetId;
        document.getElementById('edit-modal-asset-type').value = type;
        document.getElementById('edit-modal-asset-code').textContent = kode;
        document.getElementById('edit-modal-status').value = status;
        document.getElementById('edit-modal-keterangan').value = (keterangan === '-') ? '' : keterangan;
    });

    // Event saat tombol "Simpan Perubahan" di modal di-klik
    document.getElementById('edit-modal-save-button').addEventListener('click', function() {
        const assetId = document.getElementById('edit-modal-asset-id').value;
        const type = document.getElementById('edit-modal-asset-type').value;
        const newStatus = document.getElementById('edit-modal-status').value;
        const newKeterangan = document.getElementById('edit-modal-keterangan').value;
        
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        const formData = new URLSearchParams();
        formData.append(csrfName, csrfHash);
        formData.append('asset_id', assetId);
        formData.append('type', type);
        formData.append('status', newStatus);
        formData.append('keterangan', newKeterangan);

        fetch("<?= base_url('stockopname/update-session-report') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Perbarui tampilan di tabel secara langsung
                const row = document.getElementById(`row-${type}-${assetId}`);
                row.querySelector('.status-text').textContent = newStatus;
                row.querySelector('.keterangan-text').textContent = newKeterangan || '-';
                
                editReportModal.hide();
                Toast.fire({ icon: 'success', title: 'Perubahan berhasil disimpan.' });
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
        });
    });
});
</script>
<?= $this->endSection() ?>