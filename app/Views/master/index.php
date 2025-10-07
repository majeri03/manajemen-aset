<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Manajemen Data Master</h4>
    <p class="text-muted small">Kelola data referensi seperti kategori, lokasi, merk, dan tipe aset.</p>
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

<div class="chart-nav-container">
    <ul class="nav nav-pills chart-nav" id="masterDataTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kategori-tab" data-bs-toggle="tab" data-bs-target="#kategori-content" type="button" role="tab" aria-controls="kategori-content" aria-selected="true">
                <i class="bi bi-tags-fill"></i> Kategori
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="lokasi-tab" data-bs-toggle="tab" data-bs-target="#lokasi-content" type="button" role="tab" aria-controls="lokasi-content" aria-selected="false">
                <i class="bi bi-geo-alt-fill"></i> Lokasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="merk-tab" data-bs-toggle="tab" data-bs-target="#merk-content" type="button" role="tab" aria-controls="merk-content" aria-selected="false">
                <i class="bi bi-bookmark-star-fill"></i> Merk & Tipe
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="karyawan-tab" data-bs-toggle="tab" data-bs-target="#karyawan-content" type="button" role="tab">
                <i class="bi bi-people-fill"></i> Karyawan
            </button>
        </li>
    </ul>
</div>


<div class="tab-content table-container shadow-sm" id="masterDataTabsContent" style="border-top-left-radius: 0;">
    <div class="tab-pane fade show active" id="kategori-content" role="tabpanel" aria-labelledby="kategori-tab">
        <?= $this->include('master/_kategori_content') ?>
    </div>
    <div class="tab-pane fade" id="lokasi-content" role="tabpanel" aria-labelledby="lokasi-tab">
        <?= $this->include('master/_lokasi_content') ?>
    </div>
    <div class="tab-pane fade" id="merk-content" role="tabpanel" aria-labelledby="merk-tab">
        <?= $this->include('master/_merk_tipe_content') ?>
    </div>
    <div class="tab-pane fade" id="karyawan-content" role="tabpanel">
        <?= $this->include('master/_karyawan_content') ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // Script untuk menjaga tab tetap aktif setelah halaman dimuat ulang (tidak perlu diubah)
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

<?= $this->section('script') ?>
<script>
    // Script untuk menjaga tab tetap aktif setelah halaman dimuat ulang
    document.addEventListener('DOMContentLoaded', function() {
        let urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab');

        // Set default tab jika tidak ada parameter
        if (!activeTab) {
            activeTab = 'kategori';
        }
        
        let tabElement = document.querySelector('#' + activeTab + '-tab');
        if(tabElement) {
            new bootstrap.Tab(tabElement).show();
        }

        // Simpan tab terakhir yang aktif di localStorage
        var tabTriggerList = [].slice.call(document.querySelectorAll('#masterDataTabs button'));
        tabTriggerList.forEach(function (tabTriggerEl) {
            var tab = new bootstrap.Tab(tabTriggerEl);

            tabTriggerEl.addEventListener('click', function (event) {
                localStorage.setItem('activeMasterTab', this.id);
            });
        });

        // Buka tab terakhir yang aktif dari localStorage
        var lastTab = localStorage.getItem('activeMasterTab');
        if (lastTab) {
            var lastTabElement = document.getElementById(lastTab);
            if(lastTabElement) {
                var tab = new bootstrap.Tab(lastTabElement);
                tab.show();
            }
        }
    });
    
    
</script>
<?= $this->endSection() ?>