<?php
// purchases/index.php
require_once __DIR__ . '/../inc/functions.php';
require_login();

require_once __DIR__ . '/../classes/Purchase.php';
$purchaseModel = new Purchase();

$user = current_user();
// المدير يرى جميع المشتريات، العضو يرى مشترياته فقط
$purchases = is_admin() ? $purchaseModel->getAll() : $purchaseModel->getAll($user['id']);

require __DIR__ . '/../inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-bag-shopping me-2 text-success"></i>سجل المشتريات والطلبات</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم الكتاب</th>
                        <?php if (is_admin()): ?><th>المشتري</th><?php endif; ?>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                        <th>الهاتف</th>
                        <th>العنوان</th>
                        <th>تاريخ الطلب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr><td colspan="<?= is_admin() ? '8' : '7' ?>" class="text-center py-4 text-muted">لا توجد سجلات شراء حتى الآن.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($purchases as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td class="fw-bold"><?= clean_input($p['book_title']) ?></td>
                            <?php if (is_admin()): ?><td><?= clean_input($p['user_name']) ?></td><?php endif; ?>
                            <td><?= $p['quantity'] ?></td>
                            <td class="text-success fw-bold"><?= number_format($p['total'], 2) ?> ج.م</td>
                            <td><?= clean_input($p['phone']) ?></td>
                            <td><?= clean_input($p['address']) ?></td>
                            <td><?= $p['purchased_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../inc/footer.php'; ?>
