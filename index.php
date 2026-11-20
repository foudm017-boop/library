<?php
// [التجهيز وجلب البيانات]: بدء الجلسة واستدعاء الدوال وكلاس الكتب
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/Book.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$bookModel = new Book();
$search = clean_input($_GET['q'] ?? '');
$filter = clean_input($_GET['filter'] ?? 'all');

$books = [];
if (method_exists($bookModel, 'getAll')) {
    $books = $bookModel->getAll($search);
}

// فلتر الكبسولة
if ($filter !== 'all' && is_array($books)) {
    $books = array_filter($books, function($b) use ($filter) {
        $text = strtolower(($b['title'] ?? '') . ' ' . ($b['author'] ?? ''));
        if ($filter === 'arabic') {
            return preg_match('/[\x{0600}-\x{06FF}]/u', $b['title'] ?? '');
        }
        if ($filter === 'english') {
            return preg_match('/^[a-zA-Z0-9\s\-\:\.\,\!\?]+$/', $b['title'] ?? '');
        }
        if ($filter === 'programming') {
            return strpos($text, 'برمجة') !== false || strpos($text, 'php') !== false || strpos($text, 'code') !== false;
        }
        return true;
    });
}

$discountMap = [
    750 => 550,
    600 => 450,
    500 => 380,
    450 => 350,
    400 => 300,
    380 => 290,
    320 => 250,
    300 => 230,
    250 => 190,
    220 => 170,
    180 => 140
];

require_once __DIR__ . '/inc/header.php';
?>

<!-- [تنسيقات CSS الأساسية]: تركت كما هي تماماً بناءً على رغبتك لضمان عدم حدوث أي خطأ -->
<style>
    html, body, body.bg-light, .bg-white, main {
        background-color: #0f172a !important;
        color: #f8fafc !important;
    }
    .container, .row, section { background-color: transparent !important; }
    .hero-carousel-container {
        border-radius: 20px !important;
        overflow: hidden;
        border: 1px solid rgba(56, 189, 248, 0.4) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7), 0 0 30px rgba(56, 189, 248, 0.2) !important;
        margin-bottom: 3rem;
        background-color: #1e293b !important;
    }
    .carousel-item img.carousel-img {
        height: 380px !important;
        object-fit: cover !important;
        filter: brightness(0.85) contrast(1.05);
        transition: transform 1.2s ease-in-out !important;
    }
    .carousel-item.active img.carousel-img { transform: scale(1.04); }
    .custom-caption-bg {
        background: rgba(15, 23, 42, 0.85) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(56, 189, 248, 0.3);
        bottom: 20px; left: 8%; right: 8%;
    }
    .book-card, .card {
        background-color: #1e293b !important;
        border: 1px solid rgba(56, 189, 248, 0.25) !important;
        border-radius: 18px !important;
        transition: all 0.4s ease !important;
        overflow: hidden;
    }
    .book-card:hover, .card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 15px 35px rgba(56, 189, 248, 0.3) !important;
        border-color: #38bdf8 !important;
    }
    .wishlist-btn {
        position: absolute; top: 12px; left: 12px; z-index: 10;
        width: 38px; height: 38px; border-radius: 50%;
        background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.2);
        color: #f43f5e; display: flex; align-items: center; justify-content: center;
        text-decoration: none; transition: 0.3s;
    }
    .wishlist-btn:hover { background: #f43f5e; color: #fff; }
    .filter-capsule {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(56, 189, 248, 0.3);
        border-radius: 50px; padding: 6px; display: inline-flex; gap: 6px;
    }
</style>

<div class="container my-4">
    <div id="dynamicBookCarousel" class="carousel slide hero-carousel-container shadow-lg" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#dynamicBookCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#dynamicBookCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#dynamicBookCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 carousel-img" alt="...">
                <div class="carousel-caption d-none d-md-block p-4 rounded-4 custom-caption-bg">
                    <h3 class="fw-bold text-info mb-2">📚 مكتبة الكتب البرمجية</h3>
                    <p class="text-light mb-0">استعرض أحدث المراجع التقنية بكل سهولة ويسر.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 carousel-img" alt="...">
                <div class="carousel-caption d-none d-md-block p-4 rounded-4 custom-caption-bg">
                    <h3 class="fw-bold text-info mb-2">🚀 طوّر مهاراتك البرمجية</h3>
                    <p class="text-light mb-0">مصادر حصرية في لغات البرمجة الحديثة وقواعد البيانات.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 carousel-img" alt="...">
                <div class="carousel-caption d-none d-md-block p-4 rounded-4 custom-caption-bg">
                    <h3 class="fw-bold text-info mb-2">⚡ تجربة استعارة سلسة</h3>
                    <p class="text-light mb-0">تصفح الكتب واطلب كتابك المفضّل فوراً بنقرة واحدة.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2><i class="fa-solid fa-book me-2 text-info"></i>قائمة الكتب المتاحة</h2>
    <?php if (is_admin()): ?>
        <a href="<?= url('books/add.php') ?>" class="btn btn-primary rounded-pill px-4"><i class="fa-solid fa-plus me-1"></i> إضافة كتاب جديد</a>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <form method="get" action="" style="min-width: 300px; max-width: 400px;">
        <div class="input-group">
            <input type="text" name="q" class="form-control bg-dark text-white border-secondary" placeholder="ابحث بالعنوان أو المؤلف..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-info" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </form>
    <div class="filter-capsule">
        <a href="index.php?filter=all" class="btn btn-sm <?= $filter === 'all' ? 'btn-info text-dark' : 'text-light' ?> px-3 rounded-pill">الكل</a>
        <a href="index.php?filter=arabic" class="btn btn-sm <?= $filter === 'arabic' ? 'btn-info text-dark' : 'text-light' ?> px-3 rounded-pill">عربي</a>
        <a href="index.php?filter=english" class="btn btn-sm <?= $filter === 'english' ? 'btn-info text-dark' : 'text-light' ?> px-3 rounded-pill">إنجليزي</a>
        <a href="index.php?filter=programming" class="btn btn-sm <?= $filter === 'programming' ? 'btn-info text-dark' : 'text-light' ?> px-3 rounded-pill">برمجة</a>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($books)): ?>
        <div class="col-12"><div class="alert alert-warning text-center p-4">لا توجد كتب متاحة حالياً.</div></div>
    <?php endif; ?>

    <?php if (!empty($books) && is_array($books)): ?>
        <?php foreach ($books as $book): 
            $bookId = $book['id'];
            $baseAvailable = 5;
            if (method_exists($bookModel, 'getAvailableCopies')) {
                $baseAvailable = $bookModel->getAvailableCopies($bookId);
            } elseif (isset($book['copies'])) {
                $baseAvailable = $book['copies'];
            }

            $available = max(0, $baseAvailable);

            if ($available <= 0 && !is_admin()) {
                continue;
            }

            $cover = !empty($book['cover']) ? url($book['cover']) : url('assets/images/default-cover.jpg');
            $rawPrice = isset($book['price']) ? intval(floatval($book['price'])) : 0;
            $hasDiscount = array_key_exists($rawPrice, $discountMap);
            $finalPrice  = $hasDiscount ? $discountMap[$rawPrice] : $rawPrice;
        ?>
            <div class="col-md-4 col-lg-3">
                <div class="card book-card h-100 shadow-sm position-relative">
                    <a href="<?= url('wishlist.php?action=add&id=' . $bookId) ?>" class="wishlist-btn" title="إضافة للمفضلة">
                        <i class="fa-solid fa-heart"></i>
                    </a>

                    <img src="<?= clean_input($cover) ?>" class="card-img-top" style="height: 240px; object-fit: cover;" alt="...">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate"><?= clean_input($book['title']) ?></h5>
                        <p class="card-text text-muted mb-2"><i class="fa-solid fa-feather me-1 text-info"></i><?= clean_input($book['author']) ?></p>
                        
                        <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                            <?php if ($rawPrice > 0): ?>
                                <?php if ($hasDiscount): ?>
                                    <span class="text-info fw-bold fs-5"><?= $finalPrice ?> ج.م</span>
                                    <span class="text-muted text-decoration-line-through fs-6"><?= $rawPrice ?> ج.م</span>
                                    <span class="badge bg-danger">خصم</span>
                                <?php else: ?>
                                    <span class="text-info fw-bold fs-5"><?= $rawPrice ?> ج.م</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-success fw-bold">مجاني</span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <span class="badge <?= $available > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= $available > 0 ? "متاح ({$available} نسخة)" : 'نفذت النسخ' ?>
                            </span>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-1 bg-transparent">
                        <?php if (is_admin()): ?>
                            <a href="<?= url('books/edit.php?id=' . $bookId) ?>" class="btn btn-sm btn-outline-warning">تعديل</a>
                            <a href="<?= url('books/delete.php?id=' . $bookId) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('متأكد من الحذف؟');">حذف</a>
                            <a href="<?= url('show-book.php?id=' . $bookId) ?>" class="btn btn-outline-info btn-sm">التفاصيل</a>
                        <?php else: ?>
                            <a href="<?= url('orders.php?buy_book_id=' . $bookId) ?>" class="btn btn-sm btn-success">شراء</a>
                            <a href="<?= url('borrowing/borrow.php?book_id=' . $bookId) ?>" class="btn btn-sm btn-outline-primary">استعارة</a>
                            <a href="<?= url('show-book.php?id=' . $bookId) ?>" class="btn btn-outline-info btn-sm">التفاصيل</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>