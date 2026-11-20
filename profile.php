<?php
// profile.php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/Book.php';

// التأكد من تسجيل دخول المستخدم
$user = current_user();
if (!$user) {
    header('Location: ' . url('login.php'));
    exit;
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4 p-md-5 shadow" style="background-color: #1e293b; border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 20px;">
                <h2 class="text-white mb-4 fw-bold"><i class="fa-solid fa-id-card text-info me-2"></i>تفاصيل الحساب الشخصي</h2>
                
                <div class="mb-3 p-3 rounded-3" style="background-color: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.2);">
                    <label class="text-muted small d-block mb-1">الاسم بالكامل:</label>
                    <h4 class="text-white mb-0">
                        <?= clean_input(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ($user['name'] ?? $user['username']))) ?>
                    </h4>
                </div>

                <div class="mb-3 p-3 rounded-3" style="background-color: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.2);">
                    <label class="text-muted small d-block mb-1">اسم المستخدم / البريد:</label>
                    <h5 class="text-info mb-0"><?= clean_input($user['email'] ?? $user['username']) ?></h5>
                </div>

                <div class="mb-4 p-3 rounded-3" style="background-color: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.2);">
                    <label class="text-muted small d-block mb-1">نوع الحساب:</label>
                    <span class="badge bg-<?= is_admin() ? 'warning text-dark' : 'success' ?> fs-6 px-3 py-2">
                        <?= is_admin() ? 'مدير النظام (Admin)' : 'مستخدم عادي' ?>
                    </span>
                </div>

                <div class="d-flex gap-3">
                    <a href="<?= url('index.php') ?>" class="btn btn-outline-info rounded-pill px-4">العودة للرئيسية</a>
                    <a href="<?= url('logout.php') ?>" class="btn btn-outline-danger rounded-pill px-4">تسجيل خروج</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>