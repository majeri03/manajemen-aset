<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Menambahkan sedikit gaya untuk form yang lebih baik */
    .form-section-card {
        border-left: 4px solid var(--primary-blue);
    }
    .input-group-text {
        width: 42px;
        justify-content: center;
    }
</style>

<div class="main-header mb-4">
    <a href="<?= base_url('user') ?>" class="btn btn-light me-3" title="Kembali ke Daftar Pengguna"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="mb-0">Edit Pengguna</h4>
        <p class="text-muted small">Anda sedang mengubah data untuk: <strong><?= esc($user->full_name) ?></strong></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8 mx-auto">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal menyimpan!</strong>
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('user/update/' . $user->id) ?>" method="post">
            <?= csrf_field() ?>
            <div class="table-container shadow-sm p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 form-section-card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Informasi Personal</h5>
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', $user->full_name) ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user->email) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 form-section-card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Pengaturan Akun</h5>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="admin" <?= old('role', $user->role) === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="manager" <?= old('role', $user->role) === 'manager' ? 'selected' : '' ?>>Manager</option>
                                            <option value="staff" <?= old('role', $user->role) === 'staff' ? 'selected' : '' ?>>Staff</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-toggles"></i></span>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active" <?= old('status', $user->status) === 'active' ? 'selected' : '' ?>>Aktif</option>
                                            <option value="inactive" <?= old('status', $user->status) === 'inactive' ? 'selected' : '' ?>>Non-Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                         <div class="card form-section-card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Keamanan</h5>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                                    </div>
                                    <div class="form-text">Masukkan password baru jika Anda ingin mengubahnya. Minimal 8 karakter.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="<?= base_url('user') ?>" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-save-fill me-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>