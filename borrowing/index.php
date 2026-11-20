


<?php
// borrow.php حيث يتم تحويل المستخدم إليها تلقائياً فور إتمام وتأكيد طلب الاستعارة.
// [ "index", صفحة الأرشيف والمتابعة]: تعرض جدول الكتب المستعارة للمستخدم وحالتها، وترتبط بملف 
// borrowing/index.php
require_once __DIR__ . '/../inc/functions.php';
require_login();

require_once __DIR__ . '/../classes/Borrowing.php';
$borrowingModel = new Borrowing();

// التعامل مع طلب إرجاع الكتاب (خاص بالمدير أو العضو حسب رغبتك، هنا للمدير)
if (isset($_GET['return_id']) && is_admin()) {
    $borrowingModel->returnBook($_GET['return_id']);
    flash_set('تم تسجيل إرجاع الكتاب بنجاح.', 'success');
    header('Location: ' . url('borrowing/index.php'));
    exit;
}

$user = current_user();
// المدير يرى كل الاستعارات، العضو يرى استعاراته فقط
$borrowings = is_admin() ? $borrowingModel->getAll() : $borrowingModel->getAll($user['id']);

require __DIR__ . '/../inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-list-check me-2 text-primary"></i>سجل الاستعارات</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم الكتاب</th>
                        <?php if (is_admin()): ?><th>المستعير</th><?php endif; ?>
                        <th>تاريخ الاستعارة</th>
                        <th>موعد الإرجاع</th>
                        <th>الرسوم</th>
                        <th>الحالة</th>
                        <?php if (is_admin()): ?><th>إجراءات</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowings)): ?>
                        <tr><td colspan="<?= is_admin() ? '8' : '6' ?>" class="text-center py-4 text-muted">لا توجد سجلات استعارة حالياً.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($borrowings as $b): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td class="fw-bold"><?= clean_input($b['book_title']) ?></td>
                            <?php if (is_admin()): ?><td><?= clean_input($b['user_name']) ?></td><?php endif; ?>
                            <td><?= $b['borrowed_at'] ?></td>
                            <td><?= $b['due_date'] ?></td>
                            <td><?= $b['fee'] > 0 ? $b['fee'] . ' ج.م' : 'مجاني' ?></td>
                            <td>
                                <?php if ($b['returned_at']): ?>
                                    <span class="badge bg-success">تم الإرجاع (<?= $b['returned_at'] ?>)</span>
                                <?php elseif (strtotime($b['due_date']) < time()): ?>
                                    <span class="badge bg-danger">متأخر</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">قيد الاستعارة</span>
                                <?php endif; ?>
                            </td>
                            <?php if (is_admin()): ?>
                                <td>
                                    <?php if (!$b['returned_at']): ?>
                                        <a href="?return_id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-success">تسجيل إرجاع</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../inc/footer.php'; ?>