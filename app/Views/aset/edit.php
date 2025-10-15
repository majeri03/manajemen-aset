<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Edit Aset
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Edit Aset: <?= esc($aset['kode']) ?></h4>
    <p class="text-muted small">Hanya field yang diizinkan yang dapat diubah.</p>
</div>

<div class="table-container shadow-sm mb-4">
    <form action="<?= base_url('aset/' . $aset['id']) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="row g-3">
            <div class="col-md-6">
                <label for="kode" class="form-label">Kode Aset</label>
                <input type="text" class="form-control" id="kode" value="<?= esc($aset['kode']) ?>" readonly style="background-color: #e9ecef;">
            </div>
            <div class="col-md-6">
                <label for="merk_id" class="form-label">Merk</label>
                <select class="form-select" id="merk_id" name="merk_id" required>
                    <option value="">Pilih Merk</option>
                    <?php foreach ($merk_list as $merk): ?>
                        <option value="<?= $merk['id'] ?>" <?= ($aset['merk_id'] == $merk['id']) ? 'selected' : '' ?>><?= esc($merk['nama_merk']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tipe_id" class="form-label">Tipe</label>
                <select class="form-select" id="tipe_id" name="tipe_id" required>
                    <option value="">Pilih Merk Dahulu</option>
                    <?php foreach ($tipe_list as $tipe): ?>
                        <option value="<?= $tipe['id'] ?>" <?= ($aset['tipe_id'] == $tipe['id']) ? 'selected' : '' ?>><?= esc($tipe['nama_tipe']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="serial_number" class="form-label">Serial Number</label>
                <input type="text" class="form-control" id="serial_number" value="<?= esc($aset['serial_number']) ?>" readonly style="background-color: #e9ecef;">
            </div>
            <hr class="my-4">
            <div class="col-md-6">
                <label for="kategori_id" class="form-label">Kategori Barang</label>
                <select class="form-select" id="kategori_id" name="kategori_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?= $kategori['id'] ?>" <?= ($aset['kategori_id'] == $kategori['id']) ? 'selected' : '' ?>><?= esc($kategori['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="sub_kategori_id" class="form-label">Sub Kategori</label>
                <select class="form-select" id="sub_kategori_id" name="sub_kategori_id" required>
                    <option value="">Pilih Sub Kategori</option>
                    <?php foreach ($subkategori_list as $sub): ?>
                        <option value="<?= $sub['id'] ?>" <?= ($aset['sub_kategori_id'] == $sub['id']) ? 'selected' : '' ?>><?= esc($sub['nama_sub_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tahun_beli" class="form-label">Tahun Beli</label>
                <input type="number" class="form-control" id="tahun_beli" name="tahun_beli" value="<?= esc($aset['tahun_beli']) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="<?= esc($aset['harga_beli']) ?>">
            </div>
            <div class="col-md-6">
                <label for="entitas_pembelian" class="form-label">Entitas Pembelian</label>
                <input type="text" class="form-control" id="entitas_pembelian" name="entitas_pembelian" value="<?= esc($aset['entitas_pembelian']) ?>">
            </div>
            <div class="col-md-6">
                <label for="user_pengguna_display" class="form-label">User Pengguna</label>
                <input type="text" class="form-control" id="user_pengguna_display" 
                    value="<?= esc($aset['nama_karyawan'] ?? $aset['user_pengguna']) ?>" 
                    readonly style="background-color: #e9ecef;">
                <div class="form-text">User Pengguna akan otomatis diperbarui saat serah terima.</div>
            </div>
            <div class="col-md-6">
                <label for="lokasi_id" class="form-label">Lokasi</label>
                <select class="form-select" id="lokasi_id" name="lokasi_id" required>
                    <option value="">Pilih Lokasi</option>
                    <?php foreach ($lokasi_list as $lokasi): ?>
                        <option value="<?= $lokasi['id'] ?>" <?= ($aset['lokasi_id'] == $lokasi['id']) ? 'selected' : '' ?>><?= esc($lokasi['nama_lokasi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status Aset</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Baik Terpakai" <?= $aset['status'] == 'Baik Terpakai' ? 'selected' : '' ?>>Baik (Terpakai)</option>
                    <option value="Baik Tidak Terpakai" <?= $aset['status'] == 'Baik Tidak Terpakai' ? 'selected' : '' ?>>Baik (Tidak Terpakai)</option>
                    <option value="Rusak" <?= $aset['status'] == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                    <option value="Perbaikan" <?= $aset['status'] == 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                </select>
                <div class="mt-3" id="pihak-kedua-wrapper" style="display: none;">
                    <label for="pihak-kedua" class="form-label fw-bold">Pihak Kedua (Penerima)</label>
                    <div class="input-group">
                        <select class="form-select" id="pihak-kedua" name="pihak_kedua_id">
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach($karyawan_list as $karyawan): ?>
                                <option value="<?= $karyawan['id'] ?>"><?= esc($karyawan['nama_karyawan']) ?> (<?= esc($karyawan['jabatan']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-info" id="download-pdf-btn"><i class="bi bi-download"></i> Unduh PDF</button>
                    </div>
                    <div class="form-text">Pilih penerima aset untuk membuat surat serah terima.</div>
                </div>
            </div>
            <div class="col-12">
                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" oninput="this.value = this.value.toUpperCase()"><?= esc($aset['keterangan']) ?></textarea>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <a href="<?= base_url('aset') ?>" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">Dokumentasi Fisik Aset (Foto)</h5></div>
    <div class="card-body">
        <p class="text-muted small">Unggah foto yang menunjukkan kondisi fisik atau kelengkapan aset.</p>
        
        <form id="form-unggah-foto" action="<?= base_url('aset/add-dokumentasi/' . $aset['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="input-group">
                <input type="file" class="form-control" name="bukti_aset[]" multiple required accept="image/png, image/jpeg, image/jpg">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-upload me-2"></i>Unggah Foto</button>
            </div>
            <div class="form-text">Anda bisa memilih lebih dari satu file gambar (jpg, png).</div>
        </form>

        <hr>
        <h6>Dokumentasi yang Sudah Ada:</h6>
        <?php if (!empty($dokumentasi)): ?>
            <div class="row g-2 mt-2">
                <?php foreach ($dokumentasi as $doc): ?>
                    <div class="col-auto" id="doc-<?= $doc['id'] ?>">
                        <div class="position-relative">
                            <a href="<?= base_url('files/bukti/' . $doc['path_file']) ?>" target="_blank">
                                <img src="<?= base_url('files/bukti/' . $doc['path_file']) ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </a>
                             <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" style="margin: 2px;" onclick="hapusDokumen(<?= $doc['id'] ?>, 'dokumentasi')">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Belum ada dokumentasi fisik.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">Berkas Legal Aset</h5></div>
    <div class="card-body">
        <p class="text-muted small">Unggah berkas legal seperti Faktur, Surat Garansi, atau Bukti Serah Terima.</p>
        <form id="form-unggah-berkas" action="<?= base_url('aset/add-berkas/' . $aset['id']) ?>" method="post" enctype="multipart/form-data" class="mb-3">
             <?= csrf_field() ?>
            <div class="row g-2">
                <div class="col-md-5"><input type="text" name="nama_berkas" class="form-control" placeholder="Nama Berkas (Cth: Faktur Pembelian)" required></div>
                <div class="col-md-5"><input type="file" class="form-control" name="file_berkas" required accept="application/pdf, image/png, image/jpeg"></div>
                <div class="col-md-2"><button class="btn btn-outline-success w-100" type="submit">Unggah Berkas</button></div>
            </div>
        </form>
        <hr>
        <h6>Berkas yang Sudah Ada:</h6>
        <?php if (!empty($berkas_list)): ?>
            <ul class="list-group">
                <?php foreach ($berkas_list as $berkas): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" id="berkas-<?= $berkas['id'] ?>">
                        <div><i class="bi bi-file-earmark-text me-2"></i><strong><?= esc($berkas['nama_berkas']) ?></strong><small class="text-muted d-block">Diunggah pada: <?= date('d M Y', strtotime($berkas['created_at'])) ?></small></div>
                        <div>
                            <a href="<?= base_url('files/bukti/' . $berkas['path_file']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i> Lihat</a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusDokumen(<?= $berkas['id'] ?>, 'berkas')"><i class="bi bi-trash"></i></button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-muted">Belum ada berkas legal.</p>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // =================================================================
// FUNGSI GLOBAL (Bisa dipanggil dari HTML onclick)
// =================================================================
function hapusDokumen(id, type) {
    Swal.fire({
        title: 'Anda Yakin?',
        text: "File ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('aset/delete-document/') ?>${id}/${type}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    let elementId = (type === 'dokumentasi') ? `#doc-${id}` : `#berkas-${id}`;
                    $(elementId).fadeOut(300, function() { $(this).remove(); });
                    Swal.fire('Terhapus!', data.message, 'success');
                } else {
                    Swal.fire('Gagal!', data.message || 'Gagal menghapus file.', 'error');
                }
            });
        }
    });
}

$(document).ready(function() {

    // =================================================================
    // 1. FUNGSI UNGGAH FILE (AJAX) UNTUK FOTO & BERKAS
    // =================================================================
    function handleAjaxFormSubmit(formId) {
        $(formId).on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitButton = $(form).find('button[type="submit"]');
            const originalButtonContent = submitButton.html();
            let formData = new FormData(form);

            submitButton.html('<span class="spinner-border spinner-border-sm"></span> Mengunggah...').prop('disabled', true);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    // [PERBAIKAN] Tambahkan CSRF token di sini untuk keamanan
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false })
                    .then(() => { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                    submitButton.html(originalButtonContent).prop('disabled', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Error Jaringan', text: 'Tidak dapat terhubung ke server.' });
                submitButton.html(originalButtonContent).prop('disabled', false);
            });
        });
    }

    // Terapkan fungsi AJAX ke kedua form unggahan
    handleAjaxFormSubmit('#form-unggah-foto');
    handleAjaxFormSubmit('#form-unggah-berkas');

    // =================================================================
    // 2. FUNGSI UNTUK MENAMPILKAN OPSI SERAH TERIMA
    // =================================================================
    const statusDropdown = document.getElementById('status');
    const pihakKeduaWrapper = document.getElementById('pihak-kedua-wrapper');
    const pihakKeduaSelect = document.getElementById('pihak-kedua');
    const downloadButton = document.getElementById('download-pdf-btn');
    const statusAwal = '<?= esc($aset['status']) ?>';
    const asetId = '<?= esc($aset['id']) ?>';

    function togglePihakKedua() {
        const statusBaru = statusDropdown.value;
        if (statusAwal === 'Baik Tidak Terpakai' && statusBaru === 'Baik Terpakai') {
            pihakKeduaWrapper.style.display = 'block';
        } else {
            pihakKeduaWrapper.style.display = 'none';
        }
    }

    statusDropdown.addEventListener('change', togglePihakKedua);
    togglePihakKedua();

    $('form[action="<?= base_url('aset/' . $aset['id']) ?>"]').on('submit', function(e) {
        const statusBaru = $('#status').val();
        const pihakKeduaId = $('#pihak-kedua').val();

        // Cek kondisi serah terima: status berubah DAN pihak kedua kosong
        if (statusAwal === 'Baik Tidak Terpakai' && statusBaru === 'Baik Terpakai' && !pihakKeduaId) {
            e.preventDefault(); // Batalkan proses simpan
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Anda harus memilih Pihak Kedua (Penerima) untuk melanjutkan proses serah terima aset.',
            });
        }
    });

    downloadButton.addEventListener('click', function() {
        const pihakKeduaId = pihakKeduaSelect.value;
        if (!pihakKeduaId) {
            Swal.fire('Peringatan', 'Silakan pilih Pihak Kedua terlebih dahulu.', 'warning');
            return;
        }
        const url = `<?= base_url('aset/generate-pdf/') ?>${asetId}/${pihakKeduaId}`;
        window.open(url, '_blank');
    });

    // Panggil sekali saat halaman dimuat untuk memeriksa status awal
    togglePihakKedua();


    // =================================================================
    // 3. FUNGSI UNTUK SELECT2 (TAMBAH DATA MASTER BARU)
    // =================================================================
    // (Kode Select2 Anda yang sudah ada saya pindahkan ke sini agar terorganisir)
    $('#sub_kategori_id').on('select2:select', function(e) {
        var data = e.params.data;
        // Cek apakah ini adalah tag baru
        if (data.newTag === true) {
            var newSubKategori = data.text.replace(' (Baru)', '');
            var kategoriId = $('#kategori_id').val(); // Ambil ID kategori yang dipilih

            // Validasi: pastikan kategori sudah dipilih
            if (!kategoriId) {
                alert('Silakan pilih Kategori terlebih dahulu!');
                // Hapus tag baru yang salah
                var values = $('#sub_kategori_id').val();
                if (values) {
                    var index = values.indexOf(data.id);
                    if (index > -1) {
                        values.splice(index, 1);
                        $('#sub_kategori_id').val(values).trigger('change');
                    }
                }
                return;
            }

            // Kirim data ke server via AJAX
            $.ajax({
                url: "<?= site_url('master-data/add-sub-kategori') ?>", // URL ke controller
                type: "POST",
                data: {
                    nama_sub_kategori: newSubKategori,
                    kategori_id: kategoriId,
                    // Tambahkan CSRF token untuk keamanan
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Jika berhasil, perbarui opsi di Select2
                        // Hapus tag sementara
                        var values = $('#sub_kategori_id').val();
                        if (values) {
                            var index = values.indexOf(data.id);
                            if (index > -1) {
                                values.splice(index, 1);
                            }
                        }
                        // Tambahkan opsi baru yang permanen dari server
                        var newOption = new Option(response.data.text, response.data.id, true, true);
                        $('#sub_kategori_id').append(newOption).trigger('change');
                        
                        // Set nilai yang baru ditambahkan
                        values.push(response.data.id);
                        $('#sub_kategori_id').val(values).trigger('change');

                        alert('Sub Kategori baru berhasil ditambahkan dan diasosiasikan!');
                    } else {
                        alert('Gagal menambahkan sub kategori baru: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan koneksi. Gagal menambahkan sub kategori.');
                }
            });
        }
    });

    // Lakukan hal yang sama untuk Tipe yang berelasi dengan Merk
    $('#tipe_id').on('select2:select', function(e) {
        var data = e.params.data;
        if (data.newTag === true) {
            var newTipe = data.text.replace(' (Baru)', '');
            var merkId = $('#merk_id').val();

            if (!merkId) {
                alert('Silakan pilih Merk terlebih dahulu!');
                var values = $('#tipe_id').val();
                if (values) {
                    var index = values.indexOf(data.id);
                    if (index > -1) {
                        values.splice(index, 1);
                        $('#tipe_id').val(values).trigger('change');
                    }
                }
                return;
            }

            $.ajax({
                url: "<?= site_url('master-data/add-tipe') ?>",
                type: "POST",
                data: {
                    nama_tipe: newTipe,
                    merk_id: merkId,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        var values = $('#tipe_id').val();
                        if (values) {
                            var index = values.indexOf(data.id);
                            if (index > -1) {
                                values.splice(index, 1);
                            }
                        }
                        var newOption = new Option(response.data.text, response.data.id, true, true);
                        $('#tipe_id').append(newOption).trigger('change');
                        
                        values.push(response.data.id);
                        $('#tipe_id').val(values).trigger('change');

                        alert('Tipe baru berhasil ditambahkan dan diasosiasikan!');
                    } else {
                        alert('Gagal menambahkan tipe baru: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan koneksi. Gagal menambahkan tipe.');
                }
            });
        }
    });
});
   </script>
<?= $this->endSection() ?>

