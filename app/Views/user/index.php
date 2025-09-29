<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Manajemen Pengguna</h4>
    <p class="text-muted small">Kelola akun pengguna yang terdaftar di sistem.</p>
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
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user->full_name) ?></td>
                            <td><?= esc($user->email) ?></td>
                            <td><span class="badge bg-info"><?= esc(ucfirst($user->role)) ?></span></td>
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
                        <td colspan="4" class="text-center">Belum ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>