<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Reset Password
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-form-section">
        <h2 class="text-center mb-4">Buat Password Baru</h2>
        <p class="text-center mb-4">Silakan masukkan password baru Anda di bawah ini.</p>

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

        <form action="<?= base_url('reset-password/' . $token) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password Baru">
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Konfirmasi Password Baru">
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom-gradient w-100">RESET PASSWORD</button>
            </div>
        </form>

    </div>

    <div class="auth-info-section d-none d-md-flex">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo Perusahaan" class="img-fluid mb-3" style="border-radius: 10px;">
        <p>Kelola semua aset perusahaan Anda dengan mudah, efisien, dan transparan.</p>
    </div>
</div>
<?= $this->endSection() ?>