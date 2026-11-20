<?php
// borrow.php حيث يتم تحويل المستخدم إليها تلقائياً فور إتمام وتأكيد طلب الاستعارة.
// [تجهيز النظام]: التأكد من تسجيل دخول المستخدم واستدعاء الملفات الأساسية
require_once __DIR__ . '/../inc/functions.php';
require_login();

// [جلب بيانات الكتاب]: استدعاء كلاس الكتب وقراءة تفاصيل الكتاب المطلوب من الرابط
require_once __DIR__ . '/../classes/Book.php';
require_once __DIR__ . '/../classes/Borrowing.php';

$book_id = $_GET['book_id'] ?? null;
$bookModel = new Book();
$book = $bookModel->getById($book_id);

// [فحص التوافر]: التأكد من وجود الكتاب وأن لديه نسخ متاحة للاستعارة حالياً
if (!$book) {
    flash_set('الكتاب المطلوب غير موجود.', 'error');
    header('Location: ' . url('index.php'));
    exit;
}

$available = $bookModel->getAvailableCopies($book['id']);
if ($available <= 0) {
    flash_set('عذراً، هذا الكتاب غير متاح للاستعارة حالياً (جميع النسخ مستعارة).', 'error');
    header('Location: ' . url('index.php'));
    exit;
}

$errors = [];

// [حفظ الطلب]: معالجة اختيار المستخدم لمدّة الاستعارة وتسجيل العملية في قاعدة البيانات عند الضغط على تأكيد
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = (int)($_POST['days'] ?? 14);
    $due_date = date('Y-m-d', strtotime("+{$days} days"));
    $fee = $book['borrow_fee'];
    $user = current_user();

    $borrowingModel = new Borrowing();
    if ($borrowingModel->borrowBook($book['id'], $user['id'], $due_date, $fee)) {
        flash_set('تمت عملية الاستعارة بنجاح! موعد الإرجاع المستهدف: ' . $due_date, 'success');
        header('Location: ' . url('borrowing/index.php'));
        exit;
    } else {
        $errors[] = 'حدث خطأ أثناء إتمام عملية الاستعارة.';
    }
}

// [عرض التصميم]: استدعاء الهيدر الخاص بتصميم الموقع
require __DIR__ . '/../inc/header.php';
?>

<!-- [واجهة المستخدم]: نموذج اختيار مدة الاستعارة وتأكيد الطلب -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="page-title mb-4"><i class="fa-solid fa-hand-holding text-primary me-2"></i>تأكيد استعارة كتاب</h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= clean_input($error) ?></div>
            <?php endforeach; ?>

            <div class="mb-3">
                <h5>عنوان الكتاب: <span class="text-muted"><?= clean_input($book['title']) ?></span></h5>
                <p class="text-muted mb-1">المؤلف: <?= clean_input($book['author']) ?></p>
                <p class="text-primary fw-bold">رسوم الاستعارة: <?= $book['borrow_fee'] > 0 ? $book['borrow_fee'] . ' ج.م' : 'مجاناً' ?></p>
            </div>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="days" class="form-label">مدة الاستعارة (بالأيام)</label>
                    <select name="days" id="days" class="form-select">
                        <option value="7">أسبوع واحد (7 أيام)</option>
                        <option value="14" selected>أسبوعان (14 يوماً)</option>
                        <option value="30">شهر واحد (30 يوماً)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">تأكيد الاستعارة</button>
            </form>
        </div>
    </div>
</div>

<?php 
// [إنهاء الصفحة]: استدعاء الفوتر
require __DIR__ . '/../inc/footer.php'; 
?>