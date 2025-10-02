<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
Profil Pengguna
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .profile-card {
        text-align: center;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        background-color: var(--primary-blue);
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 3rem;
        font-weight: 600;
        margin: 0 auto 1rem;
        border: 4px solid var(--secondary-blue);
    }
    .form-card {
        border-left: 4px solid var(--primary-blue);
    }
</style>

<div class="main-header mb-4">
    <h4 class="mb-0">Profil Pengguna</h4>
    <p class="text-muted small">Kelola informasi akun dan keamanan Anda.</p>
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
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading">Terdapat Kesalahan Validasi:</h6>
        <ul class="mb-0 ps-4">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="table-container shadow-sm h-100 profile-card p-4">
            <div class="profile-avatar">
                <?= strtoupper(substr($user->full_name, 0, 1)) ?>
            </div>
            <h5 class="mb-1"><?= esc($user->full_name) ?></h5>
            <p class="text-muted mb-2"><?= esc($user->email) ?></p>
            <span class="badge bg-info text-dark fs-6 fw-normal"><?= esc(ucfirst($user->role)) ?></span>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="table-container shadow-sm mb-4 form-card">
            <h5 class="mb-3"><i class="bi bi-person-lines-fill me-2"></i>Ubah Informasi Akun</h5>
            <form action="<?= base_url('user/profile') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', $user->full_name) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user->email) ?>" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Informasi</button>
                </div>
            </form>
        </div>

        <div class="table-container shadow-sm form-card">
            <h5 class="mb-3"><i class="bi bi-key-fill me-2"></i>Ubah Password</h5>
            <form action="<?= base_url('user/update-password') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Masukkan password Anda yang sekarang">
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Minimal 8 karakter">
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="Ketik ulang password baru Anda">
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>