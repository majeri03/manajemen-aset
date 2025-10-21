<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Login
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-form-section">
        <h2 class="text-center mb-4">SELAMAT DATANG</h2>
        <p class="text-center mb-4">Silakan login untuk melanjutkan.</p>

        <?php if (session()->has('success')) : ?>
            <div class="alert alert-success">
                <?= session('success') ?>
            </div>
        <?php endif ?>

        <?php if (session()->has('error')) : ?>
            <div class="alert alert-danger">
                <?= session('error') ?>
            </div>
        <?php endif ?>

        <form action="/login" method="post">
            <?= csrf_field() ?>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="Alamat Email" value="<?= old('email') ?>">
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>

            <div class="text-end mb-3">
                <a href="<?= base_url('forgot-password') ?>" style="text-decoration: none; font-size: 0.9rem;">Lupa Password?</a>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom-gradient w-100">LOGIN</button>
            </div>
        </form>

        <!-- <div class="text-center mt-3">
            <p>Belum punya akun? <a href="/register" style=" text-decoration: none; font-weight: bold;">Daftar di sini</a></p>
        </div> -->
    </div>

    <div class="auth-info-section d-none d-md-flex">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Ilustrasi Manajemen Aset" class="img-fluid mb-3" style="border-radius: 10px;">
        <p>Kelola semua aset perusahaan Anda dengan mudah, efisien, dan transparan.</p>
    </div>
</div>
<?= $this->endSection() ?>