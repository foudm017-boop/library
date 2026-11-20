<?php
/**
 * [ملف User.php - مدير المستخدمين]:
 * 1. findByUsername ➔ البحث عن مستخدم في قاعدة البيانات باستخدام اسم المستخدم.
 * 2. findByEmail    ➔ البحث عن مستخدم في قاعدة البيانات باستخدام البريد الإلكتروني.
 * 3. create         ➔ تسجيل مستخدم جديد مع تشفير كلمة المرور، وتعيينه كـ "مدير (Admin)" تلقائياً لو كان أول مستخدم في النظام.
 */

// classes/User.php
require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
// * 1. findByUsername ➔ البحث عن مستخدم في قاعدة البيانات باستخدام اسم المستخدم.
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }
// * 2. findByEmail    ➔ البحث عن مستخدم في قاعدة البيانات باستخدام البريد الإلكتروني
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
//* 3. create         ➔ تسجيل مستخدم جديد مع تشفير كلمة المرور، وتعيينه كـ "مدير (Admin)" تلقائياً لو كان أول مستخدم في النظام
    public function create($data) {
        // التحقق مما إذا كان الجدول خالياً ليصبح أول مستخدم مسجل مديراً (Admin) تلقائياً
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        $role = ($userCount == 0) ? 'admin' : 'member';

        $sql = "INSERT INTO users (name, username, email, password, role) 
                VALUES (:name, :username, :email, :password, :role)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'     => $role
        ]);
    }
}