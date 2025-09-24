<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Manajemen Kategori
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-0">Manajemen Kategori Aset</h4>
        <p class="text-muted small">Kelola kategori dan sub-kategori aset Anda.</p>
    </div>
    <button class="btn btn-custom-icon" data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Kategori Baru
    </button>
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
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-container shadow-sm">
    <div class="row g-4">
        <div class="col-lg-6">
            <h5 class="mb-3">Daftar Kategori Utama</h5>
            <ul class="list-group list-group-flush">
                <?php if (!empty($kategoris)): ?>
                    <?php foreach ($kategoris as $kategori): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= esc($kategori['nama_kategori']) ?></span>
                            <div>
                                <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#tambahSubKategoriModal" data-kategori-id="<?= $kategori['id'] ?>" data-kategori-nama="<?= esc($kategori['nama_kategori']) ?>" title="Tambah Sub Kategori">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                                <a href="<?= base_url('category/deleteKategori/' . $kategori['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" title="Hapus Kategori">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </div>
                        </li>
                        <?php
                            $db = \Config\Database::connect();
                            $subKategoris = $db->table('sub_kategori')->where('kategori_id', $kategori['id'])->get()->getResultArray();
                        ?>
                        <?php if (!empty($subKategoris)): ?>
                            <ul class="list-group mt-2">
                                <?php foreach ($subKategoris as $sub): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center ms-4">
                                        <span>- <?= esc($sub['nama_sub_kategori']) ?></span>
                                        <div>
                                             <a href="<?= base_url('category/deleteSubKategori/' . $sub['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus sub-kategori ini?')" title="Hapus Sub Kategori">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-center">Belum ada kategori aset.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-labelledby="tambahKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahKategoriModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('category/createKategori') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahSubKategoriModal" tabindex="-1" aria-labelledby="tambahSubKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahSubKategoriModalLabel">Tambah Sub Kategori untuk <span id="kategoriNamaSpan"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('category/createSubKategori') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kategori_id" id="kategoriIdInput">
                    <div class="mb-3">
                        <label for="nama_sub_kategori" class="form-label">Nama Sub Kategori</label>
                        <input type="text" class="form-control" id="nama_sub_kategori" name="nama_sub_kategori" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tambahSubKategoriModal = document.getElementById('tambahSubKategoriModal');
        tambahSubKategoriModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var kategoriId = button.getAttribute('data-kategori-id');
            var kategoriNama = button.getAttribute('data-kategori-nama');
            
            var modalTitle = tambahSubKategoriModal.querySelector('.modal-title span');
            var modalInput = tambahSubKategoriModal.querySelector('#kategoriIdInput');
            
            modalTitle.textContent = kategoriNama;
            modalInput.value = kategoriId;
        });
    });
</script>
<?= $this->endSection() ?>