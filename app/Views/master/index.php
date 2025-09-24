<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Manajemen Data Master</h4>
    <p class="text-muted small">Kelola data referensi seperti kategori dan lokasi aset.</p>
</div>

<!-- Notifikasi -->
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

<!-- Navigasi Tab -->
<ul class="nav nav-tabs" id="masterDataTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="kategori-tab" data-bs-toggle="tab" data-bs-target="#kategori-content" type="button" role="tab" aria-controls="kategori-content" aria-selected="true">
            Manajemen Kategori
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="lokasi-tab" data-bs-toggle="tab" data-bs-target="#lokasi-content" type="button" role="tab" aria-controls="lokasi-content" aria-selected="false">
            Manajemen Lokasi
        </button>
    </li>
</ul>

<!-- Konten Tab -->
<div class="tab-content table-container shadow-sm" id="masterDataTabsContent" style="border-top-left-radius: 0;">
    <div class="tab-pane fade show active" id="kategori-content" role="tabpanel" aria-labelledby="kategori-tab">
        <?= $this->include('master/_kategori_content') ?>
    </div>
    <div class="tab-pane fade" id="lokasi-content" role="tabpanel" aria-labelledby="lokasi-tab">
        <?= $this->include('master/_lokasi_content') ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // Script untuk menjaga tab tetap aktif setelah halaman dimuat ulang (misalnya setelah submit form)
    document.addEventListener('DOMContentLoaded', function() {
        let urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab');

        if (activeTab) {
            let tabElement = document.querySelector('#' + activeTab + '-tab');
            if(tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        }
    });
</script>
<?= $this->endSection() ?>

