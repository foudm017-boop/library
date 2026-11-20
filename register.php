<?php
// register.php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/classes/User.php';

if (is_logged_in()) {
    header('Location: ' . url('dashboard.php'));
    exit;
}

$errors = [];
$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $username === '' || $email === '' || $password === '') {
        $errors[] = 'من فضلك املأ جميع الحقول.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صالح.';
    } elseif ($password !== $confirm) {
        $errors[] = 'كلمة المرور وتأكيدها غير متطابقين.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
    } elseif ($userModel->findByUsername($username)) {
        $errors[] = 'اسم المستخدم موجود بالفعل.';
    } elseif ($userModel->findByEmail($email)) {
        $errors[] = 'البريد الإلكتروني مستخدم بالفعل.';
    }

    if (empty($errors)) {
        if ($userModel->create(compact('name', 'username', 'email', 'password'))) {
            flash_set('تم إنشاء الحساب بنجاح، يمكنك تسجيل الدخول الآن.', 'success');
            header('Location: ' . url('login.php'));
            exit;
        } else {
            $errors[] = 'حدث خطأ أثناء إنشاء الحساب، حاول مرة أخرى.';
        }
    }
}

require __DIR__ . '/inc/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="page-title text-center mb-4"><i class="fa-solid fa-user-plus text-success me-2"></i>إنشاء حساب جديد</h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= clean_input($error) ?></div>
            <?php endforeach; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">الاسم بالكامل</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= clean_input($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= clean_input($_POST['username'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= clean_input($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2 mb-3">تسجيل الحساب</button>
            </form>
            <p class="text-center text-muted mb-0">لديك حساب بالفعل؟ <a href="<?= url('login.php') ?>" class="text-decoration-none">سجّل الدخول</a></p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>