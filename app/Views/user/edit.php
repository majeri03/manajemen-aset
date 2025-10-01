<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="main-header mb-4">
    <h4 class="mb-0">Edit Pengguna</h4>
</div>

<div class="table-container shadow-sm">
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('user/update/' . $user->id) ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="full_name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', $user->full_name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user->email) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="admin" <?= old('role', $user->role) === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="manager" <?= old('role', $user->role) === 'manager' ? 'selected' : '' ?>>Manager</option>
                    <option value="staff" <?= old('role', $user->role) === 'staff' ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?= old('status', $user->status) === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= old('status', $user->status) === 'inactive' ? 'selected' : '' ?>>Non-Aktif</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <a href="<?= base_url('user') ?>" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>