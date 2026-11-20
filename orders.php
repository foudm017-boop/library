<?php
// orders.php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/Book.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = current_user();
if (!$user) {
    header('Location: ' . url('login.php'));
    exit;
}

$bookModel = new Book();

if (!isset($_SESSION['user_purchases'])) {
    $_SESSION['user_purchases'] = [];
}

// معالجة الشراء بشكل آمن تماماً وهنا في صفحة الطلبات حصراً
$buyBookId = isset($_GET['buy_book_id']) ? intval($_GET['buy_book_id']) : 0;
if ($buyBookId > 0) {
    $allBooks = $bookModel->getAll();
    if (is_array($allBooks)) {
        foreach ($allBooks as $b) {
            if (isset($b['id']) && $b['id'] == $buyBookId) {
                $newOrder = [
                    'id' => uniqid('ord_'),
                    'book_id' => $b['id'],
                    'title' => $b['title'] ?? 'كتاب بدون عنوان',
                    'author' => $b['author'] ?? '',
                    'cover' => $b['cover'] ?? 'assets/images/default-cover.jpg',
                    'price' => $b['price'] ?? 50,
                    'date' => date('Y-m-d H:i:s'),
                    'status' => 'جاري تجهيز الطلب في الطريق إليك'
                ];
                $_SESSION['user_purchases'][] = $newOrder;
                break;
            }
        }
    }
    // إعادة توجيه نظيفة لمنع تكرار الإضافة مع الـ Refresh
    header('Location: ' . url('orders.php'));
    exit;
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="container my-5">
    <h2 class="text-white mb-4"><i class="fa-solid fa-box text-info me-2"></i>تتبع الطلبات والمشتريات</h2>

    <?php if (empty($_SESSION['user_purchases'])): ?>
        <div class="alert alert-info text-center p-5 rounded-4" style="background-color: #1e293b; border: 1px solid rgba(56,189,248,0.3); color: #f8fafc;">
            <h4>لا توجد طلبات سابقة حتى الآن</h4>
            <p class="text-muted mt-2">بمجرد قيامك بشراء أي كتاب من الموقع، ستظهر حالة الطلب وتفاصيله هنا بشكل مباشر.</p>
            <a href="<?= url('index.php') ?>" class="btn btn-outline-info mt-3 rounded-pill px-4">تصفح الكتب الآن</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach (array_reverse($_SESSION['user_purchases']) as $order): ?>
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm h-100 position-relative" style="background-color: #1e293b; border: 1px solid rgba(56,189,248,0.3); border-radius: 18px;">
                        <div class="d-flex gap-3 align-items-center flex-wrap flex-sm-nowrap">
                            <img src="<?= url($order['cover']) ?>" style="width: 90px; height: 120px; object-fit: cover; border-radius: 12px;" alt="...">
                            
                            <div class="text-white flex-grow-1">
                                <h4 class="fw-bold mb-1 text-info"><?= clean_input($order['title']) ?></h4>
                                <p class="text-muted small mb-2"><i class="fa-solid fa-feather me-1"></i><?= clean_input($order['author']) ?></p>
                                <p class="mb-2">السعر المدفوع: <strong class="text-success"><?= $order['price'] ?> ج.م</strong></p>
                                <p class="text-muted small mb-3"><i class="fa-regular fa-clock me-1"></i>تاريخ الطلب: <?= $order['date'] ?></p>
                                
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                        <i class="fa-solid fa-truck-fast me-1"></i> <?= $order['status'] ?>
                                    </span>
                                    <a href="<?= url('show-book.php?id=' . $order['book_id']) ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">عرض الكتاب</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>