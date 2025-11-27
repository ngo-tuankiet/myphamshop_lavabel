<?php
require_once 'helpers/functions.php';

// Lấy tham số
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;

// Gọi API
$url = "http://localhost:8000/api/brands?";
if ($search) {
    $url .= "&search=" . urlencode($search);
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$brands = $data['data']['data'] ?? [];
$pagination = $data['data'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thương hiệu - Beauty Lux</title>
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

        .brand-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
            height: 100%;
            border: 2px solid transparent;
        }

        .brand-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
            border-color: #dc3545;
        }

        .brand-logo {
            width: 100%;
            height: 150px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .brand-name {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        .brand-description {
            font-size: 13px;
            color: #666;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .search-box {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <!-- Header -->
    <div class="header-bg mb-4">
        <div class="container text-center">
            <h1><i class="fas fa-tags me-2"></i>Thương hiệu nổi bật</h1>
            <p class="mb-0">Khám phá các thương hiệu làm đẹp hàng đầu</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Tìm kiếm -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="search-box">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Tìm kiếm thương hiệu..."
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                        <?php if ($search): ?>
                        <a href="brands.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Xóa
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách thương hiệu -->
        <?php if (!empty($brands)): ?>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4 mb-4">
                <?php foreach ($brands as $brand): 
                    $logoUrl = '';
                    if (!empty($brand['logo']['url'])) {
                        $logoUrl = "http://localhost:8000/storage/{$brand['logo']['url']}";
                    } else {
                        $logoUrl = "https://via.placeholder.com/150?text=" . urlencode($brand['brand_name']);
                    }
                ?>
                    <div class="col">
                        <div class="brand-card" 
                             onclick="window.location.href='products.php?brand_id=<?= $brand['id'] ?>'">
                            <img src="<?= htmlspecialchars($logoUrl) ?>" 
                                 alt="<?= htmlspecialchars($brand['brand_name']) ?>"
                                 class="brand-logo"
                                 onerror="this.src='https://via.placeholder.com/150?text=No+Logo'">
                            <div class="brand-name">
                                <?= htmlspecialchars($brand['brand_name']) ?>
                            </div>
                            <?php if (!empty($brand['description'])): ?>
                            <div class="brand-description">
                                <?= htmlspecialchars($brand['description']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
            <?php if ($pagination && $pagination['last_page'] > 1):
                $curr = $pagination['current_page'];
                $last = $pagination['last_page'];
                $query = $search ? '&search=' . urlencode($search) : '';
            ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($curr > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $curr - 1 ?><?= $query ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $curr - 2); $i <= min($last, $curr + 2); $i++): ?>
                            <li class="page-item <?= $i == $curr ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $query ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($curr < $last): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $curr + 1 ?><?= $query ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h3>Không tìm thấy thương hiệu</h3>
                <p class="text-muted">Thử tìm kiếm với từ khóa khác</p>
                <?php if ($search): ?>
                <a href="brands.php" class="btn btn-danger mt-3">
                    <i class="fas fa-redo"></i> Xem tất cả thương hiệu
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>