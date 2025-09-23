<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | Manajemen Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-flex" id="wrapper" style="height: 100vh;">
        <div class="d-flex flex-column flex-shrink-0 p-3" id="sidebar">
            <div class="sidebar-header">
                <a href="/dashboard" class="d-flex align-items-center text-decoration-none">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="img-fluid">
                </a>
            </div>
                <?php 
                    // Mengambil segmen pertama dari URL untuk menentukan halaman aktif
                    // Contoh: http://localhost:8080/aset -> 'aset'
                    $current_page = service('uri')->getSegment(1); 
                ?>

                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link <?= ($current_page == 'dashboard' || $current_page == '') ? 'active' : '' ?>" aria-current="page">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('aset') ?>" class="nav-link <?= ($current_page == 'aset') ? 'active' : '' ?>">
                            <i class="bi bi-box-seam"></i> <span>Data Aset</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('tracking') ?>" class="nav-link <?= ($current_page == 'tracking') ? 'active' : '' ?>">
                            <i class="bi bi-geo-alt-fill"></i> <span>Tracking Aset</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('requests') ?>" class="nav-link <?= ($current_page == 'requests') ? 'active' : '' ?>">
                            <i class="bi bi-person-check-fill"></i> <span>Permintaan Perubahan</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('laporan') ?>" class="nav-link <?= ($current_page == 'laporan') ? 'active' : '' ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i> <span>Laporan</span>
                        </a>
                    </li>
                </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <strong><?= esc(session()->get('full_name') ?? 'Guest') ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="#">Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="main-content">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('script') ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>