<?php
require_once __DIR__ . '/inc/functions.php';

// التأكد من وجود اتصال بقاعدة البيانات
$db = Database::getInstance();

try {
    // 1. التحديث لأحدث مستخدم تم إنشاؤه ليصبح Admin
    $stmt = $db->query("SELECT id, name, email FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch();

    if ($user) {
        $update = $db->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $update->execute([$user['id']]);

        // تحديث الجلسة الحالية إذا كان هذا المستخدم هو المسجل حالياً
        if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $user['id']) {
            $_SESSION['user']['role'] = 'admin';
        }

        echo "<div style='font-family: Arial; padding: 20px; background: #e3f2fd; border-radius: 8px; margin: 20px;'>";
        echo "<h2 style='color: #0d47a1;'> تم ترقية الحساب بنجاح!</h2>";
        echo "<p><strong>الاسم:</strong> " . htmlspecialchars($user['name']) . "</p>";
        echo "<p><strong>البريد:</strong> " . htmlspecialchars($user['email']) . "</p>";
        echo "<p style='color: green;'><strong>الصلاحية الجديدة:</strong> ADMIN (مدير)</p>";
        echo "<a href='" . url('dashboard.php') . "' style='display: inline-block; padding: 10px 20px; background: #1976d2; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>الانتقال إلى لوحة التحكم</a>";
        echo "</div>";
    } else {
        echo "<h3 style='color: red;'> لا يوجد مستخدمون في قاعدة البيانات بعد. يرجى إنشاء حساب أولاً من صفحة التسجيل.</h3>";
    }
} catch (PDOException $e) {
    echo "خطأ في قاعدة البيانات: " . $e->getMessage();
}