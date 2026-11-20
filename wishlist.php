<?php
// wishlist.php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/Book.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$bookModel = new Book();
$action = $_GET['action'] ?? '';
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// تهيئة المفضلة
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// إضافة للمفضلة
if ($action === 'add' && $bookId > 0) {
    if (!in_array($bookId, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $bookId;
        if (function_exists('set_flash')) {
            set_flash('تم إضافة الكتاب إلى المفضلة بنجاح!', 'success');
        }
    }
    header('Location: ' . url('wishlist.php'));
    exit;
}

// حذف من المفضلة
if ($action === 'remove' && $bookId > 0) {
    if (($key = array_search($bookId, $_SESSION['wishlist'])) !== false) {
        unset($_SESSION['wishlist'][$key]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        if (function_exists('set_flash')) {
            set_flash('تم إزالة الكتاب من المفضلة بنجاح.', 'success');
        }
    }
    header('Location: ' . url('wishlist.php'));
    exit;
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="container my-5">
    <h2 class="text-white mb-4"><i class="fa-solid fa-heart text-danger me-2"></i>قائمة المفضلة</h2>

    <?php if (empty($_SESSION['wishlist'])): ?>
        <div class="alert alert-info text-center p-5 rounded-4" style="background-color: #1e293b; border: 1px solid rgba(56,189,248,0.3); color: #f8fafc;">
            <h4>لا توجد كتب في المفضلة حالياً</h4>
            <p class="text-muted mt-2">تصفح الكتب الرئيسية واضغط على أيقونة القلب لإضافة ما يعجبك.</p>
            <a href="<?= url('index.php') ?>" class="btn btn-outline-info mt-3 rounded-pill px-4">تصفح الكتب</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($_SESSION['wishlist'] as $id): 
                $bookList = $bookModel->getAll();
                $book = null;
                if (is_array($bookList)) {
                    foreach($bookList as $b) {
                        if(isset($b['id']) && $b['id'] == $id) { $book = $b; break; }
                    }
                }
                if(!$book) continue;
                
                // السطر رقم 35 وما حوله تم التأكد من سلامته وربط الصورة بشكل آمن
                $coverField = !empty($book['cover']) ? $book['cover'] : (!empty($book['image']) ? $book['image'] : 'assets/images/default-cover.jpg');
                $cover = function_exists('url') ? url($coverField) : $coverField;
            ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm position-relative" style="background-color: #1e293b; border: 1px solid rgba(56,189,248,0.25); border-radius: 18px;">
                        
                        <!-- زر الحذف من المفضلة -->
                        <a href="<?= url('wishlist.php?action=remove&id=' . $book['id']) ?>" class="position-absolute top-0 end-0 m-3 text-white bg-danger rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 35px; height: 35px; text-decoration: none; z-index: 10;" title="حذف من المفضلة">
                            <i class="fa-solid fa-xmark"></i>
                        </a>

                        <img src="<?= clean_input($cover) ?>" class="card-img-top" style="height: 200px; object-fit: cover; border-top-left-radius: 18px; border-top-right-radius: 18px;" alt="...">
                        
                        <div class="card-body text-white d-flex flex-column">
                            <h5 class="card-title text-truncate"><?= clean_input($book['title'] ?? '') ?></h5>
                            <p class="text-muted small mb-3"><?= clean_input($book['author'] ?? '') ?></p>
                            
                            <div class="mt-auto d-flex gap-2">
                                <a href="<?= url('show-book.php?id=' . $book['id']) ?>" class="btn btn-sm btn-outline-info w-100 rounded-pill">التفاصيل</a>
                                <a href="<?= url('purchases/buy.php?book_id=' . $book['id']) ?>" class="btn btn-sm btn-success w-100 rounded-pill">شراء</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>