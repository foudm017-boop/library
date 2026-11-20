<?php
// show-book.php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/Book.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$bookModel = new Book();
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// جلب تفاصيل الكتاب بطريقة آمنة تماماً
$book = null;
if (method_exists($bookModel, 'getById')) {
    $book = $bookModel->getById($bookId);
}

if (!$book && method_exists($bookModel, 'getAll')) {
    $allBooks = $bookModel->getAll();
    if (is_array($allBooks)) {
        foreach ($allBooks as $b) {
            if (isset($b['id']) && $b['id'] == $bookId) {
                $book = $b;
                break;
            }
        }
    }
}

// لو الكتاب مش موجود، نرجع للرئيسية
if (!$book) {
    header('Location: ' . url('index.php'));
    exit;
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

$rawPrice = isset($book['price']) ? intval(floatval($book['price'])) : 0;
$hasDiscount = array_key_exists($rawPrice, $discountMap);
$finalPrice  = $hasDiscount ? $discountMap[$rawPrice] : $rawPrice;

$cover = !empty($book['cover']) ? url($book['cover']) : url('assets/images/default-cover.jpg');

// حساب النسخ المتاحة بأمان تام
$baseAvailable = 5;
if (method_exists($bookModel, 'getAvailableCopies')) {
    $baseAvailable = $bookModel->getAvailableCopies($bookId);
} elseif (isset($book['copies'])) {
    $baseAvailable = $book['copies'];
} elseif (isset($book['quantity'])) {
    $baseAvailable = $book['quantity'];
}

$purchasedCount = 0;
if (isset($_SESSION['user_purchases']) && is_array($_SESSION['user_purchases'])) {
    foreach ($_SESSION['user_purchases'] as $p) {
        if (isset($p['book_id']) && $p['book_id'] == $bookId) {
            $purchasedCount++;
        }
    }
}
$available = max(0, $baseAvailable - $purchasedCount);

require_once __DIR__ . '/inc/header.php';
?>

<div class="container my-5">
    <div class="mb-4">
        <a href="<?= url('index.php') ?>" class="btn btn-outline-info rounded-pill px-4">
            <i class="fa-solid fa-arrow-right me-2"></i> العودة للرئيسية
        </a>
    </div>

    <div class="card p-4 p-md-5 shadow-lg" style="background-color: #1e293b; border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 20px;">
        <div class="row g-5 align-items-center">
            
            <div class="col-md-4 text-center">
                <img src="<?= clean_input($cover) ?>" class="img-fluid rounded-4 shadow" style="max-height: 400px; width: 100%; object-fit: cover; border: 1px solid rgba(56,189,248,0.2);" alt="...">
            </div>

            <div class="col-md-8 text-white">
                <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
                    <h1 class="fw-bold text-info mb-0"><?= clean_input($book['title'] ?? 'بدون عنوان') ?></h1>
                    <?php if ($purchasedCount > 0): ?>
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="fa-solid fa-fire me-1"></i> الأكثر طلباً 🔥
                        </span>
                    <?php endif; ?>
                </div>
                
                <p class="text-muted fs-5 mb-3">
                    <i class="fa-solid fa-feather me-2 text-info"></i>المؤلف: 
                    <span class="text-white"><?= clean_input($book['author'] ?? 'غير معروف') ?></span>
                </p>

                <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
                    <span class="text-muted">السعر:</span>
                    <?php if ($rawPrice > 0): ?>
                        <?php if ($hasDiscount): ?>
                            <span class="text-success fw-bold fs-3"><?= $finalPrice ?> ج.م</span>
                            <span class="text-muted text-decoration-line-through fs-5"><?= $rawPrice ?> ج.م</span>
                            <span class="badge bg-danger fs-6">خصم خاص</span>
                        <?php else: ?>
                            <span class="text-success fw-bold fs-3"><?= $rawPrice ?> ج.م</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-success fw-bold fs-3">مجاني</span>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <span class="badge <?= $available > 0 ? 'bg-success' : 'bg-danger' ?> fs-6 px-3 py-2">
                        <?= $available > 0 ? "النسخ المتاحة: {$available} نسخة" : 'نفذت النسخ' ?>
                    </span>
                </div>

                <div class="mb-4 p-4 rounded-4" style="background-color: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.2);">
                    <h5 class="text-info mb-2"><i class="fa-solid fa-circle-info me-2"></i>وصف الكتاب:</h5>
                    <p class="text-light mb-0" style="line-height: 1.8;">
                        <?= !empty($book['description']) ? nl2br(clean_input($book['description'])) : 'لا يوجد وصف متاح لهذا الكتاب حالياً.' ?>
                    </p>
                </div>

                <!-- مميزات المتجر -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 text-center h-100" style="background-color: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2);">
                            <i class="fa-solid fa-truck-fast text-info fs-4 mb-2"></i>
                            <h6 class="text-white small fw-bold mb-1">شحن سريع</h6>
                            <span class="text-muted d-block" style="font-size: 11px;">توصيل لباب المنزل</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 text-center h-100" style="background-color: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2);">
                            <i class="fa-solid fa-shield-halved text-success fs-4 mb-2"></i>
                            <h6 class="text-white small fw-bold mb-1">ضمان المنتج</h6>
                            <span class="text-muted d-block" style="font-size: 11px;">جودة أصلية 100%</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 text-center h-100" style="background-color: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2);">
                            <i class="fa-solid fa-headset text-warning fs-4 mb-2"></i>
                            <h6 class="text-white small fw-bold mb-1">دعم فني</h6>
                            <span class="text-muted d-block" style="font-size: 11px;">متاح 24/7</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 text-center h-100" style="background-color: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.2);">
                            <i class="fa-solid fa-rotate-left text-danger fs-4 mb-2"></i>
                            <h6 class="text-white small fw-bold mb-1">استبدال سهل</h6>
                            <span class="text-muted d-block" style="font-size: 11px;">خلال 14 يوم</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <?php if (is_admin()): ?>
                        <a href="<?= url('books/edit.php?id=' . $bookId) ?>" class="btn btn-warning rounded-pill px-4">تعديل الكتاب</a>
                    <?php else: ?>
                        <?php if ($available > 0): ?>
                            <a href="<?= url('orders.php?buy_book_id=' . $bookId) ?>" class="btn btn-success rounded-pill px-4">
                                <i class="fa-solid fa-cart-shopping me-2"></i> شراء الكتاب
                            </a>
                            <a href="<?= url('borrowing/borrow.php?book_id=' . $bookId) ?>" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fa-solid fa-hand-holding me-2"></i> استعارة
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary rounded-pill px-4" disabled>نفذت الكمية</button>
                        <?php endif; ?>
                        <a href="<?= url('wishlist.php?action=add&id=' . $bookId) ?>" class="btn btn-outline-danger rounded-pill px-4">
                            <i class="fa-solid fa-heart me-2"></i> المفضلة
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>