<!-- ../includes/sidebar.php -->
<div class="sidebar">
    <div class="brand">Admin Panel</div>
    <nav>
        <a href="product.php" class="<?= basename($_SERVER['PHP_SELF']) == 'product.php' ? 'active' : '' ?>">
            <i class="fas fa-box me-2"></i> Quản lý sản phẩm
        </a>
        <a href="brand.php" class="<?= basename($_SERVER['PHP_SELF']) == 'brand.php' ? 'active' : '' ?>">
            <i class="fas fa-tag me-2"></i> Quản lý thương hiệu
        </a>
        <a href="order.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order.php' ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart me-2"></i> Quản lý đơn hàng
        </a>
        <a href="user.php" class="<?= basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : '' ?>">
            <i class="fas fa-users me-2"></i> Quản lý tài khoản
        </a>
        <a href="category.php" class="<?= basename($_SERVER['PHP_SELF']) == 'category.php' ? 'active' : '' ?>">
            <i class="fas fa-list me-2"></i> Quản lý loại sản phẩm
        </a>
        <a href="#" class="text-danger">
            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
        </a>
    </nav>
</div>