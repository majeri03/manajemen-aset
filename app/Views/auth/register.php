<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Register Akun
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-form-section">
        <h2 class="text-center mb-4" style="color: var(--primary-white);">MANAJEMEN ASET</h2>
        <p class="text-center mb-4" style="color: var(--primary-white);" >Halo, Pengguna Baru!</p>
        <?php if (session()->has('error')) : ?>
            <div class="alert alert-danger">
                <?= session('error') ?>
            </div>
        <?php endif ?>
        <?php if (session()->has('errors')) : ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                <?php foreach (session('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>
        <form action="/register" method="post">
            <?= csrf_field() ?>
            
             <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="ID Karyawan" value="<?= old('employee_id') ?>" required>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nama Lengkap" value="<?= old('full_name') ?>">
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="Alamat Email" value="<?= old('email') ?>">
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Nomor Telepon (Opsional)" value="<?= old('phone_number') ?>">
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-building"></i></span>
                <input type="text" class="form-control" id="department" name="department" placeholder="Departemen (Opsional)" value="<?= old('department') ?>">
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility('password')"><i class="bi bi-eye"></i></span>
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Konfirmasi Password">
                <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility('password_confirm')"><i class="bi bi-eye"></i></span>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1" <?= old('terms') ? 'checked' : '' ?>>
                <label class="form-check-label" for="terms">Saya setuju dengan <a href="#" style="color: var(--primary-white); text-decoration: none;">Syarat & Ketentuan</a></label>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom-gradient w-100">DAFTAR AKUN</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p>Sudah punya akun? <a href="/login" style=" text-decoration: none; font-weight: bold;">Login</a></p>
        </div>
    </div>

    <div class="auth-info-section d-none d-md-flex"> <img src="<?= base_url('assets/images/logo.png') ?>" alt="Ilustrasi Manajemen Aset" class="img-fluid mb-3" style="border-radius: 10px;">
        <p>Kelola semua aset perusahaan Anda dengan mudah, efisien, dan transparan.</p>
    </div>
</div>

<script>
    function togglePasswordVisibility(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
<?= $this->endSection() ?>