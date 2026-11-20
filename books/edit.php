<?php
// books/edit.php
require_once __DIR__ . '/../inc/functions.php';
require_admin(); // المدير فقط

require_once __DIR__ . '/../classes/Book.php';

$bookModel = new Book();
$id = $_GET['id'] ?? null;
$book = $bookModel->getById($id);

if (!$book) {
    flash_set('الكتاب المطلوب غير موجود.', 'error');
    header('Location: ' . url('index.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = clean_input($_POST['title'] ?? '');
    $author     = clean_input($_POST['author'] ?? '');
    $isbn       = clean_input($_POST['isbn'] ?? '');
    $copies     = $_POST['copies'] ?? 1;
    $price      = $_POST['price'] ?? 0;
    $borrow_fee = $_POST['borrow_fee'] ?? 0;

    if ($title === '' || $author === '') {
        $errors[] = 'عنوان الكتاب واسم المؤلف حقلان إلزاميَان.';
    }

    $coverPath = $book['cover']; // الاحتفاظ بالصورة القديمة افتراضياً
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cover']['tmp_name'];
        $fileName = $_FILES['cover']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../assets/images/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // حذف الصورة القديمة إن وجدت
                if (!empty($book['cover']) && file_exists(__DIR__ . '/../' . $book['cover'])) {
                    @unlink(__DIR__ . '/../' . $book['cover']);
                }
                $coverPath = 'assets/images/' . $newFileName;
            }
        } else {
            $errors[] = 'صيغة الصورة غير مدعومة.';
        }
    }

    if (empty($errors)) {
        $data = [
            'title'      => $title,
            'author'     => $author,
            'isbn'       => $isbn,
            'copies'     => $copies,
            'price'      => $price,
            'borrow_fee' => $borrow_fee,
            'cover'      => $coverPath
        ];

        if ($bookModel->update($id, $data)) {
            flash_set('تم تحديث بيانات الكتاب بنجاح!', 'success');
            header('Location: ' . url('index.php'));
            exit;
        } else {
            $errors[] = 'حدث خطأ أثناء التحديث.';
        }
    }
}

require __DIR__ . '/../inc/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="page-title mb-4"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>تعديل بيانات الكتاب</h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= clean_input($error) ?></div>
            <?php endforeach; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">عنوان الكتاب <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= clean_input($book['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label">المؤلف <span class="text-danger">*</span></label>
                    <input type="text" id="author" name="author" class="form-control" value="<?= clean_input($book['author']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="isbn" class="form-label">الرقم التسلسلي (ISBN)</label>
                        <input type="text" id="isbn" name="isbn" class="form-control" value="<?= clean_input($book['isbn']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="copies" class="form-label">عدد النسخ</label>
                        <input type="number" id="copies" name="copies" class="form-control" min="1" value="<?= clean_input($book['copies']) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">سعر الشراء (ج.م)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= clean_input($book['price']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="borrow_fee" class="form-label">رسوم الاستعارة (ج.م)</label>
                        <input type="number" step="0.01" id="borrow_fee" name="borrow_fee" class="form-control" value="<?= clean_input($book['borrow_fee']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cover" class="form-label">صورة الغلاف الجديدة (اتركها فارغة للإبقاء على الحالية)</label>
                    <input type="file" id="cover" name="cover" class="form-control" accept="image/*">
                    <?php if (!empty($book['cover'])): ?>
                        <div class="mt-2">
                            <img src="<?= url($book['cover']) ?>" alt="الغلاف الحالي" style="height: 80px;" class="rounded border">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-warning w-100 py-2">تعديل وحفظ التغييرات</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../inc/footer.php'; ?>
