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
    <div class="page-wrapper">
        <div class="d-flex flex-column flex-shrink-0 p-3" id="sidebar">
            <div class="sidebar-header">
                <a href="/dashboard" class="d-flex align-items-center text-decoration-none">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="img-fluid logo-full">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Icon" class="img-fluid logo-icon" style="display: none; max-width: 40px;">
                </a>
            </div>
            <hr>
                <?php 
                    $current_page = service('uri')->getSegment(1); 
                    $manajemen_aset_active = in_array($current_page, ['aset', 'master-data', 'import']);
                    $operasional_active = in_array($current_page, ['tracking', 'requests', 'laporan']);
                ?>

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link <?= ($current_page == 'dashboard' || $current_page == '') ? 'active' : '' ?>" aria-current="page">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#aset-submenu" data-bs-toggle="collapse" class="nav-link <?= !$manajemen_aset_active ? 'collapsed' : '' ?>">
                        <i class="bi bi-archive-fill"></i> <span>Manajemen Aset</span>
                    </a>
                    <div class="collapse <?= $manajemen_aset_active ? 'show' : '' ?>" id="aset-submenu">
                        <ul class="nav flex-column ms-3">
                            <li><a href="<?= base_url('aset') ?>" class="nav-link submenu-link <?= ($current_page == 'aset') ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> <span>Data Aset</span></a></li>
                            <li><a href="<?= base_url('master-data') ?>" class="nav-link submenu-link <?= ($current_page == 'master-data') ? 'active' : '' ?>"><i class="bi bi-hdd-stack-fill"></i> <span>Data Master</span></a></li>
                            <li><a href="<?= base_url('import') ?>" class="nav-link submenu-link <?= ($current_page == 'import') ? 'active' : '' ?>"><i class="bi bi-file-earmark-excel"></i> <span>Import Data</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="#operasional-submenu" data-bs-toggle="collapse" class="nav-link <?= !$operasional_active ? 'collapsed' : '' ?>">
                        <i class="bi bi-gear-fill"></i> <span>Operasional</span>
                    </a>
                    <div class="collapse <?= $operasional_active ? 'show' : '' ?>" id="operasional-submenu">
                        <ul class="nav flex-column ms-3">
                            <li><a href="<?= base_url('tracking') ?>" class="nav-link submenu-link <?= ($current_page == 'tracking') ? 'active' : '' ?>"><i class="bi bi-geo-alt-fill"></i> <span>Tracking Aset</span></a></li>
                            <li>
                                <a href="<?= base_url('requests') ?>" class="nav-link submenu-link <?= ($current_page == 'requests') ? 'active' : '' ?> d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person-check-fill"></i> <span>Permintaan</span></span>
                                    <?php if (isset($pending_requests) && $pending_requests > 0): ?>
                                        <span class="badge bg-danger rounded-pill"><?= $pending_requests ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li><a href="<?= base_url('laporan') ?>" class="nav-link submenu-link <?= ($current_page == 'laporan') ? 'active' : '' ?>"><i class="bi bi-file-earmark-bar-graph"></i> <span>Laporan</span></a></li>
                        </ul>
                    </div>
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
        
        <div class="main-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light d-lg-none sticky-top shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="mobileSidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <a class="navbar-brand ms-3" href="#">Manajemen Aset</a>
                </div>
            </nav>

            <div class="main-content">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const overlay = document.querySelector('.sidebar-overlay');
            const sidebar = document.getElementById('sidebar');

            if (mobileSidebarToggle) {
                // Tampilkan sidebar saat tombol hamburger diklik
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                });

                // Sembunyikan sidebar saat overlay diklik
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        });
    </script>
    <?= $this->renderSection('script') ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>