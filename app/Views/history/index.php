<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Container Timeline */
    .timeline-page {
        list-style: none;
        padding: 20px 0;
        position: relative;
        margin: 0;
    }

    /* Garis Vertikal Abu-abu di Tengah Ikon */
    .timeline-page:before {
        top: 0;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 3px;
        background-color: #e9ecef;
        left: 25px; /* Posisi pas di tengah badge lebar 50px */
        margin-left: -1.5px;
        z-index: 0;
    }

    /* ITEM BARIS (Flex Container) - Ini KUNCI agar tidak bertumpuk */
    .timeline-page > li {
        margin-bottom: 20px;
        position: relative;
        display: flex;  /* Menggunakan Flexbox */
        align-items: flex-start; /* Ratakan atas */
        gap: 20px; /* Jarak antara ikon dan panel */
        z-index: 1;
    }

    /* IKON BULAT (BADGE) */
    .timeline-page > li > .timeline-badge {
        width: 50px;
        height: 50px;
        flex-shrink: 0; /* Mencegah ikon mengecil/gepeng */
        
        color: #fff;
        display: flex;            /* Flexbox untuk menengahkan ikon di dalam lingkaran */
        align-items: center;
        justify-content: center;
        font-size: 1.4em;
        
        background-color: #fff;
        border-radius: 50%;
        border: 3px solid #e9ecef;
        position: relative; /* Agar tampil di atas garis vertikal */
        z-index: 2;
    }

    /* KOTAK KONTEN (PANEL) */
    .timeline-page > li > .timeline-panel {
        flex-grow: 1; /* Panel mengisi sisa ruang */
        
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        background-color: #fff;
    }

    /* Panah Kecil di Kiri Panel (Pemanis) */
    .timeline-panel:before {
        content: " ";
        position: absolute;
        top: 20px;
        right: 100%;
        height: 0;
        width: 0;
        border: 7px solid transparent;
        border-right-color: #e9ecef;
    }
    .timeline-panel:after {
        content: " ";
        position: absolute;
        top: 21px;
        right: 100%;
        height: 0;
        width: 0;
        border: 6px solid transparent;
        border-right-color: #fff;
    }
    
    /* Text Waktu */
    .timeline-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>

<div class="main-header mb-4">
    <h4 class="mb-0">Riwayat Aktivitas Aset</h4>
    <p class="text-muted small">Jejak rekam lengkap perjalanan aset perusahaan.</p>
</div>

<div class="row">
    <div class="col-12">
        <ul class="nav nav-pills mb-4 gap-2" id="historyTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" onclick="filterHistory('all')">Semua</button></li>
            
            <li class="nav-item"><button class="nav-link" onclick="filterHistory('input')"><i class="bi bi-plus-circle me-1"></i> Input Baru</button></li>
            
            <li class="nav-item"><button class="nav-link" onclick="filterHistory('mutasi')">Mutasi</button></li>
            <li class="nav-item"><button class="nav-link" onclick="filterHistory('stock_opname')">Stock Opname</button></li>
            <li class="nav-item"><button class="nav-link" onclick="filterHistory('penjualan')">Penjualan</button></li>
            <li class="nav-item"><button class="nav-link" onclick="filterHistory('pemusnahan')">Pemusnahan</button></li>
        </ul>

        <?php if (empty($history)): ?>
            <div class="alert alert-info text-center">Belum ada aktivitas aset yang tercatat.</div>
        <?php else: ?>
            <ul class="timeline-page">
                <?php foreach ($history as $item): ?>
                    <li class="history-item" data-type="<?= $item['type'] ?>">
                        <div class="timeline-badge <?= $item['color'] ?> shadow-sm">
                            <i class="bi <?= $item['icon'] ?>"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold <?= $item['color'] ?>"><?= $item['title'] ?></h6>
                                <span class="timeline-time"><i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i', strtotime($item['date'])) ?></span>
                            </div>
                            <p class="mb-1"><?= $item['desc'] ?></p>
                            <small class="text-muted">Oleh: <?= esc($item['user']) ?></small>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
function filterHistory(type) {
    // 1. Hapus active class dari semua tombol
    document.querySelectorAll('.nav-link').forEach(btn => btn.classList.remove('active'));
    
    // 2. Tambahkan active class ke tombol yang diklik
    // (Menggunakan event.currentTarget agar aman jika ada icon di dalam tombol)
    event.currentTarget.classList.add('active');

    const items = document.querySelectorAll('.history-item');
    
    items.forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            // PERBAIKAN UTAMA DI SINI:
            // Jangan gunakan 'block', tapi gunakan 'flex' agar layout tetap rapi
            item.style.display = 'flex'; 
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
<?= $this->endSection() ?>