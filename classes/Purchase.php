<?php
/**
 * [ملف Purchase.php - مدير عمليات الشراء]:
 * 1. create ➔ تسجيل طلب شراء جديد لكتاب في قاعدة البيانات (مع تفاصيل الكمية، السعر، والعنوان).
 * 2. getAll  ➔ جلب وعرض قائمة المشتريات (سواء لكل المشتريات أو لمشتريات مستخدم معين) مع تفاصيل الكتاب واسم المستخدم.
 */

require_once __DIR__ . '/Database.php';

class Purchase {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
// * 1. create ➔ تسجيل طلب شراء جديد لكتاب في قاعدة البيانات (مع تفاصيل الكمية، السعر، والعنوان).

    public function create($data) {
        $sql = "INSERT INTO purchases (book_id, user_id, quantity, unit_price, total, full_name, phone, address, notes) 
                VALUES (:book_id, :user_id, :quantity, :unit_price, :total, :full_name, :phone, :address, :notes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'book_id'    => (int)$data['book_id'],
            'user_id'    => (int)$data['user_id'],
            'quantity'   => (int)$data['quantity'],
            'unit_price' => (float)$data['unit_price'],
            'total'      => (float)$data['total'],
            'full_name'  => $data['full_name'],
            'phone'      => $data['phone'],
            'address'    => $data['address'],
            'notes'      => $data['notes'] ?? ''
        ]);
    }
//2. getAll  ➔ جلب وعرض قائمة المشتريات (سواء لكل المشتريات أو لمشتريات مستخدم معين) مع تفاصيل الكتاب واسم المستخدم.
    public function getAll($user_id = null) {
        if ($user_id) {
            $sql = "SELECT p.*, bk.title as book_title, u.name as user_name 
                    FROM purchases p 
                    JOIN books bk ON p.book_id = bk.id 
                    JOIN users u ON p.user_id = u.id 
                    WHERE p.user_id = :user_id 
                    ORDER BY p.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => (int)$user_id]);
        } else {
            $sql = "SELECT p.*, bk.title as book_title, u.name as user_name 
                    FROM purchases p 
                    JOIN books bk ON p.book_id = bk.id 
                    JOIN users u ON p.user_id = u.id 
                    ORDER BY p.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
}