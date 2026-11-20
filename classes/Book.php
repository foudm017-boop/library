

<?php
// (Inheritance) الوراثه
//​ملف Book.php (مدير مخزن الكتب):
//  ​1. getAll ➔ جلب كل الكتب والبحث فيها بالاسم أو المؤلف.
// ​2. getById ➔ إحضار تفاصيل كتاب محدد باستخدام رقمه (ID).
// ​3. add ➔ إضافة كتاب جديد لقاعدة البيانات ببياناته وغلافه.
// ​4. update ➔ تعديل وتحديث بيانات أو غلاف كتاب موجود مسبقاً.
// ​5. delete ➔ حذف كتاب نهائياً من النظام.
// ​6. getAvailableCopies ➔ حساب عدد النسخ المتاحة فعلياً للاستعارة.
// [التوصيل بقاعدة البيانات]: تهيئة الاتصال بالداتابيز عند إنشاء كائن جديد من الكلاس
require_once __DIR__ . '/Database.php';

class Book {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // [عرض الكتب]: جلب كل الكتب من قاعدة البيانات مع إمكانية البحث بالعنوان أو المؤلف
    public function getAll($search = '') {
        if ($search !== '') {
            $stmt = $this->db->prepare("SELECT * FROM books WHERE title LIKE :q OR author LIKE :q ORDER BY id DESC");
            $stmt->execute(['q' => "%{$search}%"]);
        } else {
            $stmt = $this->db->query("SELECT * FROM books ORDER BY id DESC");
        }
        return $stmt->fetchAll();
    }

    // [جلب كتاب برقم هـ (ID)]: البحث عن كتاب معين وإحضار تفاصيله بالكامل
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    // [إضافة كتاب جديد]: إدخال كتاب جديد ببياناته (العنوان، المؤلف، النسخ، الأسعار، الغلاف) إلى الداتابيز
    public function add($data) {
        $sql = "INSERT INTO books (title, author, isbn, copies, price, borrow_fee, cover) 
                VALUES (:title, :author, :isbn, :copies, :price, :borrow_fee, :cover)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title'      => $data['title'],
            'author'     => $data['author'],
            'isbn'       => $data['isbn'] ?? '',
            'copies'     => (int)($data['copies'] ?? 1),
            'price'      => (float)($data['price'] ?? 0),
            'borrow_fee' => (float)($data['borrow_fee'] ?? 0),
            'cover'      => $data['cover'] ?? null
        ]);
    }

    // [تعديل بيانات كتاب]: تحديث معلومات كتاب موجود مسبقاً وتغيير بياناته أو غلافه
    public function update($id, $data) {
        $data['id'] = (int)$id;
        $sql = "UPDATE books SET title = :title, author = :author, isbn = :isbn, 
                copies = :copies, price = :price, borrow_fee = :borrow_fee";
        
        if (!empty($data['cover'])) {
            $sql .= ", cover = :cover";
        } else {
            unset($data['cover']);
        }
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // [حذف كتاب]: إزالة كتاب من قاعدة البيانات نهائياً برقم الـ ID الخاص به
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    // [حساب النسخ المتاحة]: طرح عدد النسخ المستعارة حالياً من إجمالي نسخ الكتاب لمعرفة المتاح للاستعارة
    public function getAvailableCopies($id) {
        $book = $this->getById($id);
        if (!$book) return 0;
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM borrowings WHERE book_id = :id AND returned_at IS NULL");
        $stmt->execute(['id' => (int)$id]);
        $borrowed = $stmt->fetchColumn();

        return max(0, (int)$book['copies'] - (int)$borrowed);
    }
}