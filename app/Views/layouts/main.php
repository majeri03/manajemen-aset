<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | Manajemen Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-blue: #003481ff; 
        --secondary-blue: #3da2ff;
        --white: #FFFFFF;
        --light-grey: #f4f7f6;
        --dark-text: #0D1B2A;
    }

    body {
        background-color: var(--light-grey);
        font-family: 'Poppins', sans-serif; /* MENGGUNAKAN FONT BARU */
    }

    .auth-container {
        display: flex;
        min-height: 100vh;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .auth-card {
        background-color: var(--white);
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        display: flex;
    }

    /* BAGIAN FORM (SEKARANG BIRU) */
    .auth-form-section {
        flex: 1;
        background-color: var(--primary-blue);
        color: var(--white);
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .auth-form-section h2 {
        font-weight: 600;
    }
    .auth-form-section .form-control { /* Style input di background biru */
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        color: var(--dark-text);
    }
    .auth-form-section .form-control::placeholder {
        color: #6c757d;
    }
    .auth-form-section .form-check-label,
    .auth-form-section p,
    .auth-form-section a {
        color: var(--white);
    }

    /* BAGIAN INFO (SEKARANG PUTIH) */
    .auth-info-section {
        flex: 1;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        gap: 20px;
        color: var(--dark-text);
    }
    .auth-info-section h3 {
        font-weight: 600;
    }

    /* TOMBOL DENGAN GRADIENT */
    .btn-custom-gradient {
        background-image: linear-gradient(to right, var(--secondary-blue) 0%, var(--primary-blue) 51%, var(--secondary-blue) 100%);
        background-size: 200% auto;
        color: white;
        border: none;
        padding: 10px 20px;
        font-weight: bold;
        border-radius: 8px;
        transition: 0.5s;
    }
    .btn-custom-gradient:hover {
        background-position: right center; /* Ganti arah gradient saat hover */
        color: #fff;
        text-decoration: none;
    }
    
    /* Input Styling Umum */
    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
    }
    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-check-input:checked {
        background-color: var(--secondary-blue);
        border-color: var(--secondary-blue);
    }

    /* Responsiveness */
    @media (max-width: 768px) {
        .auth-card {
            flex-direction: column;
        }
        .auth-info-section {
            order: -1;
        }
    }
</style>
</head>
<body class="auth-container">

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>