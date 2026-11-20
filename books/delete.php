<?php
// books/delete.php
require_once __DIR__ . '/../inc/functions.php';
require_admin();

require_once __DIR__ . '/../classes/Book.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $bookModel = new Book();
    $book = $bookModel->getById($id);
    
    if ($book) {
        // حذف صورة الغلاف من السيرفر إذا وجدت
        if (!empty($book['cover']) && file_exists(__DIR__ . '/../' . $book['cover'])) {
            @unlink(__DIR__ . '/../' . $book['cover']);
        }
        
        $bookModel->delete($id);
        flash_set('تم حذف الكتاب بنجاح.', 'success');
    }
}

header('Location: ' . url('index.php'));
exit;
