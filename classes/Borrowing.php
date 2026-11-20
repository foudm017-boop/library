\<?php
/**
 * [ملف Borrowing.php - مدير عمليات الاستعارة]:
 * 1. borrow      ➔ تسجيل عملية استعارة جديدة لكتاب بواسطة مستخدم.
 * 2. returnBook  ➔ تسجيل تاريخ إرجاع الكتاب وإعادة النسخة للمخزون.
 * 3. getAll      ➔ جلب وعرض كل سجلات وعمليات الاستعارة في النظام.
 */

require_once __DIR__ . '/Database.php';

class Borrowing {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // [تسجيل استعارة جديدة]: إضافة عملية استعارة جديدة للجدول
    public function borrow($data) {
        $sql = "INSERT INTO borrowings (book_id, user_id, borrowed_at, due_date) 
                VALUES (:book_id, :user_id, NOW(), :due_date)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'book_id'  => (int)$data['book_id'],
            'user_id'  => (int)$data['user_id'],
            'due_date' => $data['due_date']
        ]);
    }

    // [إرجاع الكتاب]: تحديث وقت الإرجاع ليكون الوقت الحالي
    public function returnBook($id) {
        $sql = "UPDATE borrowings SET returned_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => (int)$id]);
    }

    //    admin  [جلب سجلات الاستعارة]: عرض كل الاستعارات مع ربطها ببيانات الكتب والمستخدمين
    public function getAll($user_id = null) {
        if ($user_id) {
            $sql = "SELECT b.*, bk.title as book_title, u.name as user_name 
                    FROM borrowings b 
                    JOIN books bk ON b.book_id = bk.id 
                    JOIN users u ON b.user_id = u.id 
                    WHERE b.user_id = :user_id 
                    ORDER BY b.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => (int)$user_id]);
        } else {
            $sql = "SELECT b.*, bk.title as book_title, u.name as user_name 
                    FROM borrowings b 
                    JOIN books bk ON b.book_id = bk.id 
                    JOIN users u ON b.user_id = u.id 
                    ORDER BY b.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
}