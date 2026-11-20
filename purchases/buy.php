<?php
// purchases/buy.php
require_once __DIR__ . '/../inc/functions.php';
require_login();

require_once __DIR__ . '/../classes/Book.php';
require_once __DIR__ . '/../classes/Purchase.php';

$book_id = $_GET['book_id'] ?? null;
$bookModel = new Book();
$book = $bookModel->getById($book_id);

if (!$book) {
    flash_set('الكتاب المطلوب غير موجود.', 'error');
    header('Location: ' . url('index.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity  = (int)($_POST['quantity'] ?? 1);
    $full_name = clean_input($_POST['full_name'] ?? '');
    $phone     = clean_input($_POST['phone'] ?? '');
    $address   = clean_input($_POST['address'] ?? '');
    $notes     = clean_input($_POST['notes'] ?? '');

    if ($full_name === '' || $phone === '' || $address === '') {
        $errors[] = 'يرجى إكمال بيانات الشحن والاتصال الإلزامية.';
    } elseif ($quantity < 1) {
        $errors[] = 'الكمية يجب أن تكون 1 على الأقل.';
    }

    if (empty($errors)) {
        $user = current_user();
        $unit_price = $book['price'];
        $total = $unit_price * $quantity;

        $purchaseModel = new Purchase();
        $data = [
            'book_id'    => $book['id'],
            'user_id'    => $user['id'],
            'quantity'   => $quantity,
            'unit_price' => $unit_price,
            'total'      => $total,
            'full_name'  => $full_name,
            'phone'      => $phone,
            'address'    => $address,
            'notes'      => $notes
        ];

        if ($purchaseModel->create($data)) {
            flash_set('تم إتمام عملية الشراء بنجاح! شكراً لك.', 'success');
            header('Location: ' . url('purchases/index.php'));
            exit;
        } else {
            $errors[] = 'حدث خطأ أثناء معالجة الطلب.';
        }
    }
}

$user = current_user();
require __DIR__ . '/../inc/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="page-title mb-4"><i class="fa-solid fa-cart-shopping text-success me-2"></i>شراء كتاب</h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= clean_input($error) ?></div>
            <?php endforeach; ?>

            <div class="mb-4 bg-light p-3 rounded">
                <h5>الكتاب: <span class="text-primary"><?= clean_input($book['title']) ?></span></h5>
                <p class="text-muted mb-1">المؤلف: <?= clean_input($book['author']) ?></p>
                <p class="fw-bold mb-0">سعر النسخة: <?= $book['price'] > 0 ? number_format($book['price'], 2) . ' ج.م' : 'مجاني' ?></p>
            </div>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="quantity" class="form-label">الكمية</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1" required>
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">الاسم الكامل للاستلام</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?= clean_input($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">عنوان الشحن بالتفصيل</label>
                    <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات إضافية (اختياري)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2">إتمام الشراء الآن</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../inc/footer.php'; ?>