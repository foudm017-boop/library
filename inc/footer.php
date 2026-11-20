<?php
// inc/footer.php
?>
</main>
<footer class="site-footer bg-dark text-white py-4 mt-auto">
    <div class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> نظام إدارة المكتبة - جميع الحقوق محفوظة.</p>
    </div>
</footer>

<!-- JS Assets -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>



<footer class="site-footer text-light pt-5 pb-3 mt-5" style="background: linear-gradient(180deg, #0f172a 0%, #090d16 100%); border-top: 3px solid #38bdf8;">
    <div class="container">
        <div class="row">
            
            <!-- معلومات المكتبة والعنوان -->
            <div class="col-md-4 mb-4">
                <h5 class="text-info fw-bold mb-3">📚 مكتبة الكتب البرمجية</h5>
                <p class="text-muted" style="color: #94a3b8 !important;">وجهتك الأولى لاحتراف البرمجة، استعارة، وشراء أحدث المراجع التقنية بكل سهولة وسرعة.</p>
                <p class="text-light mb-1">📍 <strong>العنوان:</strong> القاهرة، مصر</p>
                <p class="text-light">🏢 <strong>عدد الفروع:</strong> 3 فروع رئيسية</p>
            </div>

            <!-- روابط سريعة -->
            <div class="col-md-4 mb-4">
                <h5 class="text-info fw-bold mb-3">🔗 روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-decoration-none text-muted" style="color: #94a3b8 !important;">الرئيسية</a></li>
                    <li class="mb-2"><a href="cart.php" class="text-decoration-none text-muted" style="color: #94a3b8 !important;">المشتريات / السلة</a></li>
                    <li class="mb-2"><a href="contact.php" class="text-decoration-none text-muted" style="color: #94a3b8 !important;">تواصل معنا</a></li>
                </ul>
            </div>

            <!-- بيانات التواصل والحسابات -->
            <div class="col-md-4 mb-4">
                <h5 class="text-info fw-bold mb-3">📞 تواصل معنا والحسابات</h5>
                <p class="text-muted mb-2" style="color: #94a3b8 !important;">📱 <strong>الهاتف / واتساب:</strong> +20 100 000 0000</p>
                <p class="text-muted mb-2" style="color: #94a3b8 !important;">📧 <strong>البريد الإلكتروني:</strong> support@bookstore.com</p>
                <div class="social-icons mt-3">
                    <a href="#" class="text-info me-3 fs-5"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-info me-3 fs-5"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-info me-3 fs-5"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="text-info fs-5"><i class="fab fa-github"></i></a>
                </div>
            </div>

        </div>

        <hr class="border-secondary my-3" style="border-color: rgba(255,255,255,0.1) !important;">
        <div class="text-center text-muted" style="color: #94a3b8 !important;">
            <small>جميع الحقوق محفوظة &copy; 2026 - نظام إدارة المكتبة البرمجية</small>
        </div>
    </div>
</footer>
