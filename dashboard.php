<?php
// dashboard.php
require_once __DIR__ . '/inc/functions.php';
require_login(); // التأكد من تسجيل الدخول

$user = current_user();

require __DIR__ . '/inc/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
            <div class="container-fluid py-3">
                <h1 class="display-5 fw-bold text-primary">مرحباً بك، <?= clean_input($user['name']) ?></h1>
                <p class="col-md-8 fs-4 text-muted">
                    <?php if (is_admin()): ?>
                        أنت مطلع على لوحة التحكم بصلاحيات **مدير النظام**. يمكنك إضافة الكتب، تعديلها، ومتابعة استعارات ومشتريات الأعضاء.
                    <?php else: ?>
                        أنت مسجل كعضو في المكتبة. يمكنك تصفح الكتب المتاحة، استعارتها، أو شرائها بسهولة.
                    <?php endif; ?>
                </p>
                <hr class="my-4">
                <div class="d-flex gap-3">
                    <a href="<?= url('index.php') ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-book me-2"></i>تصفح الكتب</a>
                    <?php if (is_admin()): ?>
                        <a href="<?= url('books/add.php') ?>" class="btn btn-success btn-lg"><i class="fa-solid fa-plus me-2"></i>إضافة كتاب جديد</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>