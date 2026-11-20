<?php
// books/add.php
require_once __DIR__ . '/../inc/functions.php';
require_admin(); // المدير فقط يستطيع إضافة كتب

require_once __DIR__ . '/../classes/Book.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = clean_input($_POST['title'] ?? '');
    $author      = clean_input($_POST['author'] ?? '');
    $isbn        = clean_input($_POST['isbn'] ?? '');
    $copies      = $_POST['copies'] ?? 1;
    $price       = $_POST['price'] ?? 0;
    $borrow_fee  = $_POST['borrow_fee'] ?? 0;
    $description = clean_input($_POST['description'] ?? ''); // استلام الوصف

    if ($title === '' || $author === '') {
        $errors[] = 'عنوان الكتاب واسم المؤلف حقلان إلزاميَان.';
    }

    // معالجة رفع صورة الغلاف (اختياري)
    $coverPath = null;
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
                $coverPath = 'assets/images/' . $newFileName;
            }
        } else {
            $errors[] = 'صيغة الصورة غير مدعومة. استخدم (jpg, png, webp).';
        }
    }

    if (empty($errors)) {
        $bookModel = new Book();
        $data = [
            'title'       => $title,
            'author'      => $author,
            'isbn'        => $isbn,
            'copies'      => $copies,
            'price'       => $price,
            'borrow_fee'  => $borrow_fee,
            'description' => $description, // حفظ الوصف في الداتابيز
            'cover'       => $coverPath
        ];

        if ($bookModel->add($data)) {
            flash_set('تم إضافة الكتاب بنجاح!', 'success');
            header('Location: ' . url('index.php'));
            exit;
        } else {
            $errors[] = 'حدث خطأ أثناء حفظ الكتاب في قاعدة البيانات.';
        }
    }
}

require __DIR__ . '/../inc/header.php';
?>

<div class="row justify-content-center my-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 p-4" style="background-color: #1e293b; color: #f8fafc; border: 1px solid rgba(56,189,248,0.3) !important; border-radius: 20px;">
            <h2 class="page-title mb-4 text-success"><i class="fa-solid fa-plus me-2"></i>إضافة كتاب جديد</h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= clean_input($error) ?></div>
            <?php endforeach; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label text-white">عنوان الكتاب <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" class="form-control bg-dark text-white border-secondary" value="<?= clean_input($_POST['title'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label text-white">المؤلف <span class="text-danger">*</span></label>
                    <input type="text" id="author" name="author" class="form-control bg-dark text-white border-secondary" value="<?= clean_input($_POST['author'] ?? '') ?>" required>
                </div>
                
                <!-- حقل وصف الكتاب أو نبذة عنه عند الإضافة -->
                <div class="mb-3">
                    <label for="description" class="form-label text-white">وصف الكتاب / نبذة عنه:</label>
                    <textarea id="description" name="description" class="form-control bg-dark text-white border-secondary" rows="4" placeholder="اكتب نبذة مختصرة عن محتوى الكتاب وما يتناوله..."><?= clean_input($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="isbn" class="form-label text-white">الرقم التسلسلي (ISBN)</label>
                        <input type="text" id="isbn" name="isbn" class="form-control bg-dark text-white border-secondary" value="<?= clean_input($_POST['isbn'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="copies" class="form-label text-white">عدد النسخ</label>
                        <input type="number" id="copies" name="copies" class="form-control bg-dark text-white border-secondary" min="1" value="<?= clean_input($_POST['copies'] ?? 1) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label text-white">سعر الشراء (ج.م)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control bg-dark text-white border-secondary" value="<?= clean_input($_POST['price'] ?? 0) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="borrow_fee" class="form-label text-white">رسوم الاستعارة (ج.م)</label>
                        <input type="number" step="0.01" id="borrow_fee" name="borrow_fee" class="form-control bg-dark text-white border-secondary" value="<?= clean_input($_POST['borrow_fee'] ?? 0) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cover" class="form-label text-white">صورة الغلاف</label>
                    <input type="file" id="cover" name="cover" class="form-control bg-dark text-white border-secondary" accept="image/*">
                </div>
                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">حفظ وإضافة الكتاب</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../inc/footer.php'; ?>