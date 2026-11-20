<?php
// inc/header.php
require_once __DIR__ . '/functions.php';
$flash = flash_get();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المكتبة</title>
    <!-- CSS Assets & Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">

    <style>
    /* --- 1. الخلفية العامة والنصوص الأساسية واضحة --- */
    html, body, body.bg-light {
        background-color: #172540 !important;
        color: #f1f5fa !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- 2. الشريط العلوي (Navbar) --- */
    .navbar, .site-header, nav {
        background: linear-gradient(135deg, #111b29 0%, #0f1527 100%) !important;
        border-bottom: 2px solid rgba(206, 219, 224, 0.3) !important;
    }
    .navbar .nav-link, .navbar-brand {
        color: #ffffff !important;
    }
    .navbar .nav-link:hover {
        color: #2abfff !important;
    }

    /* --- 3. كروت الكتب الأصلية: تفاعل، حركة نيون، ونصوص واضحة --- */
    .book-card, .card {
        background-color: #301435 !important;
        border: 1px solid rgb(253, 247, 247) !important;
        border-radius: 18px !important;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        overflow: hidden;
    }

    .book-card:hover, .card:hover {
        transform: translateY(-12px) scale(1.02) !important;
        box-shadow: 0 15px 35px rgba(56, 189, 248, 0.4), 
                    0 0 25px rgba(56, 189, 248, 0.3) !important;
        border-color: #38bdf8 !important;
    }

    .book-card img.card-img-top, .card img.card-img-top {
        transition: transform 0.6s ease, filter 0.6s ease !important;
    }
    .book-card:hover img.card-img-top, .card:hover img.card-img-top {
        transform: scale(1.07) !important;
        filter: brightness(1.12) !important;
    }

    .book-card .card-body, .book-card .card-footer, .card .card-body, .card .card-footer {
        background-color: transparent !important;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .book-card .card-title, .card .card-title, h5 {
        color: rgb(249, 226, 226) !important;
        font-weight: 700 !important;
    }

    .book-card .card-text, .book-card .text-muted, .book-card p, 
    .card .card-text, .card .text-muted, .card p {
        color: #ffffff !important;
    }

    /* --- التفاعل والحركة النيون للروابط والأزرار العلوية (Navbar) --- */
    .navbar a, 
    .navbar .nav-link, 
    .site-header a, 
    nav a {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        border-radius: 8px !important;
    }

    .navbar a:hover, 
    .navbar .nav-link:hover, 
    .site-header a:hover, 
    nav a:hover {
        color: #38bdf8 !important;
        background-color: rgba(56, 189, 248, 0.12) !important;
        transform: translateY(-3px) !important;
        text-shadow: 0 0 12px rgba(56, 189, 248, 0.7) !important;
        box-shadow: 0 4px 15px rgba(56, 189, 248, 0.25) !important;
    }
 
 /* --- إجبار النصوص والعناوين في صفحات الدخول والإضافة على أن تكون فاتحة وواضحة --- */
    body:not(:has(.book-card)) form label,
    body:not(:has(.book-card)) .card-body,
    body:not(:has(.book-card)) h1, 
    body:not(:has(.book-card)) h2, 
    body:not(:has(.book-card)) h3, 
    body:not(:has(.book-card)) h4, 
    body:not(:has(.book-card)) .form-label,
    .login-container label,
    .login-box label {
        color: #f8fafc !important;
    }

    /* إجبار حقول الإدخال في تلك الصفحات لتكون واضحة */
    body:not(:has(.book-card)) .form-control {
        background-color: #1e293b !important;
        color: #ffffff !important;
        border-color: rgba(56, 189, 248, 0.4) !important;
    }
 
 
 
 
 </style>
</head>

<body class="bg-light">
<header class="site-header bg-dark text-white py-3 mb-4 shadow">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?= url('index.php') ?>" class="text-white text-decoration-none h4 mb-0 fw-bold">
            <i class="fa-solid fa-book-journal-whills text-warning me-2"></i>نظام المكتبة
        </a>
        <?php if ($user): ?>
            <nav class="nav align-items-center gap-2">
                <a href="<?= url('dashboard.php') ?>" class="nav-link text-white">الرئيسية</a>
                <a href="<?= url('books/index.php') ?>" class="nav-link text-white">الكتب</a>
                <a href="<?= url('borrowing/index.php') ?>" class="nav-link text-white">الاستعارات</a>
                <a href="<?= url('purchases/index.php') ?>" class="nav-link text-white">المشتريات</a>
                <?php if (is_admin()): ?>
                    <a href="<?= url('books/add.php') ?>" class="nav-link text-white"><i class="fa-solid fa-plus me-1"></i>إضافة كتاب</a>
                <?php endif; ?>

                <!-- العنصر رقم (6): قائمة الحساب المنسدلة (الاسم الأول والأخير + الخيارات المطلوبة) -->
                <div class="dropdown ms-2">
                    <button class="btn btn-outline-info dropdown-toggle rounded-pill px-3 py-1 text-white" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: #38bdf8;">
                        <i class="fa-solid fa-user-circle me-1 text-warning"></i> 
                        <?php 
                            // جلب الاسم الأول والأخير أو اسم المستخدم كبديل
                            $firstName = $user['first_name'] ?? ($user['name'] ?? $user['username']);
                            $lastName  = $user['last_name'] ?? '';
                            echo clean_input($firstName . ' ' . $lastName);
                        ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-secondary mt-2" aria-labelledby="accountDropdown" style="background-color: #1e293b; border-radius: 12px;">
                        <li><a class="dropdown-item text-light py-2" href="<?= url('orders.php') ?>"><i class="fas fa-box me-2 text-info"></i> تتبع الطلبات</a></li>
                        <li><a class="dropdown-item text-light py-2" href="<?= url('profile.php') ?>"><i class="fas fa-id-card me-2 text-info"></i> تفاصيل الحساب</a></li>
                        <li><a class="dropdown-item text-light py-2" href="<?= url('wishlist.php') ?>"><i class="fas fa-heart me-2 text-danger"></i> المفضلة</a></li>
                        <li><hr class="dropdown-divider border-secondary my-1"></li>
                        <li><a class="dropdown-item text-danger py-2 fw-bold" href="<?= url('logout.php') ?>"><i class="fas fa-sign-out-alt me-2"></i> تسجيل خروج</a></li>
                    </ul>
                </div>
            </nav>
        <?php else: ?>
            <div>
                <a href="<?= url('login.php') ?>" class="btn btn-outline-light btn-sm me-2">دخول</a>
                <a href="<?= url('register.php') ?>" class="btn btn-warning btn-sm">حساب جديد</a>
            </div>
        <?php endif; ?>
    </div>
</header>
<main class="container mb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= clean_input($flash['type'] === 'error' ? 'danger' : $flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= clean_input($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>