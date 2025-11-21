<?php
require_once 'helpers/functions.php';
require_once 'components/product-card.php';
require_once 'components/product-section.php';
require_once 'components/slider-section.php';
require_once 'components/brand-section.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Mỹ Phẩm Đơn Giản</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-light">
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <!-- Banner Section -->
        <div class="row">
            <div class="col-8">
                <img src="../index3.jpg" class="img-fluid w-100" />
            </div>
            <div class="col-4 d-flex flex-column justify-content-between">
                <img src="../index2.webp" class="img-fluid w-100 mb-2" />
                <img src="../index1.webp" class="img-fluid w-100" />
            </div>
        </div>

        <!-- Hot Products Slider -->
        <?php renderSliderSection('SẢN PHẨM HOT', getHotProducts(5)); ?>

        <!-- THÊM PHẦN NÀY - Thương hiệu nổi bật -->
        <?php renderBrandSection('THƯƠNG HIỆU NỔI BẬT', getBrands(6)); ?>

        <!-- Trang Điểm Section -->
        <?php
        renderProductSection([
            'sidebarPosition' => 'left',
            'sidebarTitle' => 'TRANG ĐIỂM',
            'sidebarImage' => '../uploads/banner/Trang_diem.jpg',
            'tabs' => ['TRANG ĐIỂM MẶT', 'TRANG ĐIỂM MẮT'],
            'products' => getMakeupProducts(8)
        ]);
        ?>

        <!-- Son Môi Section -->
        <?php
        renderProductSection([
            'sidebarPosition' => 'right',
            'sidebarTitle' => 'SON MÔI',
            'sidebarImage' => '../uploads/banner/Son_moi.jpg',
            'tabs' => ['SON THỎI', 'SON KEM', 'SON DƯỠNG', 'SON BÓNG'],
            'products' => getLipstickProducts()
        ]);
        ?>

        <!-- Chăm Sóc Da Section -->
        <?php
        renderProductSection([
            'sidebarPosition' => 'left',
            'sidebarTitle' => 'CHĂM SÓC DA',
            'sidebarImage' => '../uploads/banner/Cham_soc_da.jpg',
            'tabs' => ['LÀM SẠCH', 'DƯỠNG DA', 'MẶT NẠ', 'KEM CHỐNG NẮNG'],
            'products' => getSkincareProducts()
        ]);
        ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>