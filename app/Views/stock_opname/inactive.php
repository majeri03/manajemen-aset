<?= $this->extend('layouts/main_dashboard') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container text-center" style="margin-top: 15%;">
    <i class="bi bi-slash-circle-fill text-danger" style="font-size: 5rem;"></i>
    <h1 class="mt-4">Akses Ditolak</h1>
    <p class="lead">
        <?= esc($message ?? 'Fitur Stock Opname saat ini sedang tidak aktif. Silakan kembali lagi nanti atau hubungi administrator.') ?>
    </p>
    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
</div>
<?= $this->endSection() ?>