<?php
// login.php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/User.php';

if (is_logged_in()) {
    header('Location: ' . url('dashboard.php'));
    exit;
}

$errors = [];
$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $userModel->findByUsername($username);

    if (!$user || !password_verify($password, $user['password'])) {
        $errors[] = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    } else {
        unset($user['password']); // إزالة كلمة المرور للأمان
        $_SESSION['user'] = $user;
        
        flash_set('تم تسجيل الدخول بنجاح، مرحباً بك!', 'success');
        header('Location: ' . url('dashboard.php'));
        exit;
    }
}

require __DIR__ . '/inc/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="page-title text-center mb-4"><i class="fa-solid fa-right-to-bracket text-primary me-2"></i>تسجيل الدخول</h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= clean_input($error) ?></div>
            <?php endforeach; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= clean_input($_POST['username'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">دخول</button>
            </form>
            <p class="text-center text-muted mb-0">ليس لديك حساب؟ <a href="<?= url('register.php') ?>" class="text-decoration-none">إنشاء حساب جديد</a></p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>