<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'BPKBSys - Mega Central Finance & Mega Auto Finance' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">

    <style>
        :root {
            /* Warna Identitas Berdasarkan Logo MACF (Terang & Cerah) */
            --brand-yellow: #FFCA28;   /* Kuning MACF */
            --brand-orange: #F57C00;   /* Oranye MACF */
            
            /* Warna Pendukung Elemen UI (Light Theme) */
            --bg-light: #F8F9FA;       /* Background konten utama yang bersih */
            --surface-white: #FFFFFF;  /* Background untuk card dan panel */
            --text-main: #495057;      /* Teks utama, tidak terlalu hitam pekat */
            --text-muted: #868E96;     /* Teks sekunder/keterangan */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
        }

        /* Kustomisasi komponen umum agar senada dengan logo */
        .btn-primary {
            background-color: var(--brand-orange) !important;
            border-color: var(--brand-orange) !important;
            color: #FFFFFF !important;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(245, 124, 0, 0.2);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #E65100 !important; /* Oranye lebih gelap saat di-hover */
            border-color: #E65100 !important;
            transform: translateY(-1px);
        }

        .text-brand {
            color: var(--brand-orange) !important;
        }

        /* Kustomisasi Badge */
        .badge-maf {
            background-color: #FFF3E0; /* Latar oranye sangat pudar */
            color: var(--brand-orange);
            border: 1px solid #FFE0B2;
            font-weight: 600;
        }

        .badge-mcf {
            background-color: #FFF8E1; /* Latar kuning sangat pudar */
            color: #D48806; /* Kuning pekat/emas */
            border: 1px solid #FFECB3;
            font-weight: 600;
        }

        /* Form input focus style */
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 0.25rem rgba(245, 124, 0, 0.15);
        }
    </style>
</head>
<body>