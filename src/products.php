<?php
require_once 'helpers/functions.php';
require_once 'components/product-card.php';

// Lấy tham số
$search = $_GET['search'] ?? '';
$brandId = $_GET['brand_id'] ?? '';
$categoryId = $_GET['category_id'] ?? '';
$page = $_GET['page'] ?? 1;

// Gọi API
$url = "http://localhost:8000/api/products?";
if ($search)
    $url .= "&search=" . urlencode($search);
if ($brandId)
    $url .= "&brand_id={$brandId}";
if ($categoryId)
    $url .= "&category_id={$categoryId}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$products = $data['data']['data'] ?? [];
$pagination = $data['data'] ?? null;

// Lấy brands
$ch = curl_init("http://localhost:8000/api/brands");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$brandsResponse = curl_exec($ch);
curl_close($ch);
$brandsData = json_decode($brandsResponse, true);
$brands = $brandsData['data']['data'] ?? $brandsData['data'] ?? [];

// Lấy categories
$ch = curl_init("http://localhost:8000/api/categories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$categoriesResponse = curl_exec($ch);
curl_close($ch);
$categoriesData = json_decode($categoriesResponse, true);
$categories = $categoriesData['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - Beauty Lux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
        }

        .header-bg {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px 0;
        }

        .product-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            height: 100%;
            transition: 0.3s;
        }

        .product-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Header -->
    <div class="header-bg mb-4">
        <div class="container">
            <h1>Danh sách Sản phẩm</h1>
            <p class="mb-0">Tìm thấy <?= count($products) ?> sản phẩm</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Bộ lọc -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..."
                                value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="brand_id" class="form-select">
                                <option value="">-- Thương hiệu --</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= $brandId == $b['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b['brand_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="category_id" class="form-select">
                                <option value="">-- Danh mục --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $categoryId == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-search"></i> Lọc
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <?php if (!empty($products)): ?>
            <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
                <?php foreach ($products as $p):
                    $img = '';
                    if (!empty($p['images'])) {
                        $firstImg = is_array($p['images'][0]) ? $p['images'][0]['url'] : $p['images'][0];
                        $img = $firstImg;
                    }

                    $product = [
                        'id' => $p['id'],
                        'name' => $p['name'],
                        'brand' => $p['brand']['brand_name'] ?? 'N/A',
                        'price' => $p['price'],
                        'image' => $img
                    ];
                    ?>
                    <div class="col">
                        <div class="product-item">
                            <?php renderProductCard($product); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
            <?php if ($pagination && $pagination['last_page'] > 1):
                $curr = $pagination['current_page'];
                $last = $pagination['last_page'];

                // Giữ filter
                $params = [];
                if ($search)
                    $params[] = "search=" . urlencode($search);
                if ($brandId)
                    $params[] = "brand_id={$brandId}";
                if ($categoryId)
                    $params[] = "category_id={$categoryId}";
                $query = $params ? '&' . implode('&', $params) : '';
                ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($curr > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $curr - 1 ?><?= $query ?>">«</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $curr - 2); $i <= min($last, $curr + 2); $i++): ?>
                            <li class="page-item <?= $i == $curr ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $query ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($curr < $last): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $curr + 1 ?><?= $query ?>">»</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h3>Không tìm thấy sản phẩm</h3>
                <a href="products.php" class="btn btn-danger mt-3">Xóa bộ lọc</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>