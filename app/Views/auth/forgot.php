<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Lupa Password
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-form-section">
        <h2 class="text-center mb-4">Lupa Password Anda?</h2>
        <p class="text-center mb-4">Tidak masalah. Masukkan alamat email Anda di bawah ini dan kami akan mengirimkan tautan untuk mereset password Anda.</p>

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

        <form action="<?= base_url('forgot-password') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Alamat Email Anda" value="<?= old('email') ?>" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-custom-gradient w-100">Kirim Link Reset</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p>Kembali ke halaman <a href="<?= base_url('login') ?>" style=" text-decoration: none; font-weight: bold;">Login</a></p>
        </div>
    </div>

    <div class="auth-info-section d-none d-md-flex">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo Perusahaan" class="img-fluid mb-3" style="border-radius: 10px;">
        <p>Kelola semua aset perusahaan Anda dengan mudah, efisien, dan transparan.</p>
    </div>
</div>
<?= $this->endSection() ?>