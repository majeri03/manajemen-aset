<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .user-card { transition: all 0.3s ease; border: 1px solid #e9ecef; }
    .user-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }
    .user-avatar { width: 60px; height: 60px; border-radius: 50%; background-color: var(--secondary-blue); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600; }
    .status-badge { font-size: 0.8rem; }
</style>

<div class="main-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="mb-0">Manajemen Pengguna</h4>
        <p class="text-muted small mb-0">Kelola akun, peran, dan status pengguna sistem.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form action="<?= base_url('user') ?>" method="get" class="d-flex gap-2">
            <input type="text" name="keyword" class="form-control" placeholder="Cari nama atau email..." value="<?= esc($filters['keyword'] ?? '') ?>">
            <select name="role" class="form-select" style="width: 150px;">
                <option value="">Semua Role</option>
                <option value="super_admin" <?= ($filters['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="staff" <?= ($filters['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
            <a href="<?= base_url('user') ?>" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </form>
        <a href="<?= base_url('user/create') ?>" class="btn btn-success flex-shrink-0" title="Tambah Pengguna Baru">
            <i class="bi bi-plus-lg"></i>
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="mb-1"><i class="bi bi-shield-check me-2"></i>Mode Stock Opname Global</h5>
            <p class="mb-0 text-muted small">Aktifkan mode ini agar pengguna yang berizin dapat melakukan verifikasi aset melalui pemindaian QR Code.</p>
        </div>
        <div class="form-check form-switch fs-4">
            <input class="form-check-input" type="checkbox" id="globalSoModeSwitch" <?= $so_mode === 'on' ? 'checked' : '' ?>>
            <label class="form-check-label" for="globalSoModeSwitch"></label>
        </div>
    </div>
</div>

<div class="table-container shadow-sm">
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        <h5 class="mb-0 me-3 text-muted">Ringkasan Pengguna</h5>
        <div class="badge bg-primary fs-6 fw-normal">Total: <?= $totalUsers ?></div>
        <div class="badge bg-success fs-6 fw-normal">Aktif: <?= $statusCounts['active'] ?></div>
        <div class="badge bg-secondary fs-6 fw-normal">Non-Aktif: <?= $statusCounts['inactive'] ?></div>
        <div class="vr mx-2"></div>
        <div class="badge bg-info text-dark fs-6 fw-normal">Super Admin: <?= $roleCounts['super_admin'] ?></div>
        <div class="badge bg-info text-dark fs-6 fw-normal">Admin: <?= $roleCounts['admin'] ?></div>
        <div class="badge bg-info text-dark fs-6 fw-normal">Staff: <?= $roleCounts['staff'] ?></div>
    </div>
    <hr class="mt-0 mb-4">

    <div class="row g-4">
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <div class="col-xl-6 col-lg-12">
                    <div class="card shadow-sm user-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="user-avatar me-3">
                                    <?= strtoupper(substr($user->full_name, 0, 1)) ?>
                                </div>
                                <div>
                                    <h5 class="mb-0 card-title"><?= esc($user->full_name) ?></h5>
                                    <p class="mb-0 text-muted small"><?= esc($user->email) ?></p>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <?php if ($user->status === 'active'): ?>
                                        <span class="badge bg-success status-badge"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger status-badge"><i class="bi bi-x-circle-fill"></i> Non-Aktif</span>
                                    <?php endif; ?>
                                    <span class="badge bg-info status-badge"><i class="bi bi-person-fill"></i> <?= esc(ucfirst($user->role)) ?></span>
                                </div>
                                <div class="form-check form-switch" title="Izin Stock Opname">
                                    <input class="form-check-input user-so-permission" type="checkbox" data-userid="<?= $user->id ?>" <?= $user->can_perform_so ? 'checked' : '' ?>>
                                </div>
                            </div>

                            <div class="mt-auto d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="<?= base_url('user/edit/' . $user->id) ?>" class="btn btn-outline-warning btn-sm"><i class="bi bi-pencil-fill"></i> Edit</a>
                                <form action="<?= base_url('user/reset-password/' . $user->id) ?>" method="post" class="d-inline" onsubmit="return confirmReset(event, '<?= esc($user->full_name) ?>');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-key-fill"></i> Reset Pass</button>
                                </form>
                                 <?php if ($user->status === 'active'): ?>
                                    <form action="<?= base_url('user/delete/' . $user->id) ?>" method="post" class="d-inline" onsubmit="return confirmDeactivate(event, '<?= esc($user->full_name) ?>');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-person-x-fill"></i> Non-Aktifkan</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= base_url('user/activate/' . $user->id) ?>" method="post" class="d-inline" onsubmit="return confirmActivate(event, '<?= esc($user->full_name) ?>');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-success btn-sm"><i class="bi bi-person-check-fill"></i> Aktifkan</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center">
                    <i class="bi bi-person-exclamation fs-3"></i>
                    <h5 class="mt-2">Pengguna Tidak Ditemukan</h5>
                    <p class="text-muted">Tidak ada pengguna yang cocok dengan kriteria pencarian atau filter Anda.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // FUNGSI KONFIRMASI RESET PASSWORD
    function confirmReset(event, name) {
        event.preventDefault();
        Swal.fire({
            title: 'Anda Yakin?',
            html: `Password untuk pengguna "<b>${name}</b>" akan direset. Anda tidak dapat membatalkan tindakan ini.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset Password!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }

    // FUNGSI KONFIRMASI NONAKTIFKAN
    function confirmDeactivate(event, name) {
        event.preventDefault();
        Swal.fire({
            title: 'Nonaktifkan Pengguna?',
            html: `Pengguna "<b>${name}</b>" tidak akan bisa login lagi. Anda yakin?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Non-Aktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }
    
    // FUNGSI KONFIRMASI AKTIFKAN (BARU)
    function confirmActivate(event, name) {
        event.preventDefault();
        Swal.fire({
            title: 'Aktifkan Pengguna?',
            html: `Pengguna "<b>${name}</b>" akan dapat login kembali ke sistem. Anda yakin?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Aktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }

    // Script untuk toggle izin Stock Opname (tetap sama)
    document.addEventListener('DOMContentLoaded', function() {
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
    
        function sendRequest(url, body) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: body
            }).then(response => response.json());
        }
    
        const globalSwitch = document.getElementById('globalSoModeSwitch');
        if (globalSwitch) {
            globalSwitch.addEventListener('change', function() {
                const newStatus = this.checked ? 'on' : 'off';
                const body = new URLSearchParams({ [csrfName]: csrfHash, 'status': newStatus });
    
                sendRequest('<?= base_url('user/toggle-so-mode') ?>', body)
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                        this.checked = !this.checked;
                    }
                });
            });
        }
    
        const userSwitches = document.querySelectorAll('.user-so-permission');
        userSwitches.forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const userId = this.dataset.userid;
                const hasPermission = this.checked;
                const body = new URLSearchParams({ [csrfName]: csrfHash, 'permission': hasPermission });
    
                sendRequest(`<?= base_url('user/toggle-so-permission/') ?>${userId}`, body)
                .then(data => {
                    if (!data.success) {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                        this.checked = !hasPermission;
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>