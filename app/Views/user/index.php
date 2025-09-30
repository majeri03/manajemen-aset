<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Manajemen Pengguna & Izin</h4>
    <p class="text-muted small">Kelola akun pengguna dan atur izin untuk fitur Stock Opname.</p>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Mode Stock Opname Global</h5>
            <p class="mb-0 text-muted small">Aktifkan mode ini agar pengguna yang berizin dapat melakukan verifikasi aset.</p>
        </div>
        <div class="form-check form-switch fs-4">
            <input class="form-check-input" type="checkbox" id="globalSoModeSwitch" <?= $so_mode === 'on' ? 'checked' : '' ?>>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="table-container shadow-sm">
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= base_url('user/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Tambah Pengguna
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th>Role</th>
                    <th class="text-center">Izin Stock Opname</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user->full_name) ?></td>
                            <td class="d-none d-md-table-cell"><?= esc($user->email) ?></td>
                            <td><span class="badge bg-info"><?= esc(ucfirst($user->role)) ?></span></td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input user-so-permission" type="checkbox" data-userid="<?= $user->id ?>" <?= $user->can_perform_so ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <a href="<?= base_url('user/edit/' . $user->id) ?>" class="btn btn-warning btn-sm" title="Edit Pengguna">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="<?= base_url('user/delete/' . $user->id) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" class="btn btn-danger btn-sm" title="Hapus Pengguna">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';

    function sendRequest(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body
        }).then(response => response.json());
    }

    // Handler untuk saklar global
    const globalSwitch = document.getElementById('globalSoModeSwitch');
    if (globalSwitch) {
        globalSwitch.addEventListener('change', function() {
            const newStatus = this.checked ? 'on' : 'off';
            const body = new URLSearchParams({
                [csrfName]: csrfHash,
                'status': newStatus
            });

            sendRequest('<?= base_url('user/toggle-so-mode') ?>', body)
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                    this.checked = !this.checked; // Kembalikan jika gagal
                }
            });
        });
    }

    // Handler untuk saklar per pengguna
    const userSwitches = document.querySelectorAll('.user-so-permission');
    userSwitches.forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            const userId = this.dataset.userid;
            const hasPermission = this.checked;
            const body = new URLSearchParams({
                [csrfName]: csrfHash,
                'permission': hasPermission
            });

            sendRequest(`<?= base_url('user/toggle-so-permission/') ?>${userId}`, body)
            .then(data => {
                if (!data.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
                    this.checked = !hasPermission; // Kembalikan jika gagal
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>