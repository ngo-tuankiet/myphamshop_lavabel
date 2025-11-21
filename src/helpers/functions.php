<?php
// Thông tin kết nối CSDL
$servername = "localhost"; // Địa chỉ máy chủ CSDL (thường là localhost)
$username = "root";       // Tên người dùng CSDL mặc định của XAMPP
$password = "";           // Mật khẩu CSDL (mặc định XAMPP là rỗng, nếu bạn đã đặt mật khẩu, hãy nhập vào đây)
$dbname = "ecommerce"; // Tên CSDL bạn vừa tạo ở bước 2

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

mysqli_set_charset($conn, "utf8mb4");

function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . 'đ';
}

function getHotProducts($limit = 5)
{
    global $conn;

    // Sửa lỗi ONLY_FULL_GROUP_BY bằng cách dùng truy vấn phụ để lấy ảnh đại diện
    $sql = "SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.description, 
                p.stock, 
                b.brand_name AS brand_name,
                pi.url 
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN (
                SELECT 
                    product_id, 
                    MIN(url) AS url 
                FROM productimage 
                GROUP BY product_id
            ) pi ON p.id = pi.product_id
            ORDER BY p.id DESC 
            LIMIT " . (int) $limit;

    $result = $conn->query($sql);

    // Kiểm tra lỗi (Quan trọng)
    if ($result === false) {
        die("LỖI TRUY VẤN: " . $conn->error . "<br>SQL đã chạy: " . $sql);
    }

    // ... Phần xử lý kết quả
    $products_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products_data[] = [
                'id' => $row['id'],
                'image' => $row['url'] ?? '../default.webp',
                'name' => $row['name'],
                'brand' => $row['brand_name'] ?? 'Chưa xác định',
                'price' => $row['price'],
                'formatted_price' => formatPrice($row['price']),
                // ...
            ];
        }
    }

    return $products_data;
}

function getBrands($limit = 6)
{
    global $conn;

    // Sửa lại truy vấn, sử dụng LEFT JOIN với Subquery để lấy ảnh đại diện
    $sql = "SELECT 
                b.id, 
                b.brand_name,
                bi.url 
            FROM brands b
            LEFT JOIN (
                -- Truy vấn con: Lấy ảnh đại diện (MIN(url)) cho mỗi brand_id
                SELECT 
                    brand_id, 
                    MIN(url) AS url 
                FROM brandimage 
                GROUP BY brand_id
            ) bi ON b.id = bi.brand_id
            ORDER BY b.brand_name ASC 
            LIMIT " . (int) $limit; // Giới hạn kết quả

    $result = $conn->query($sql);

    // Kiểm tra lỗi (rất quan trọng)
    if ($result === false) {
        die("LỖI TRUY VẤN BRANDS: " . $conn->error . "<br>SQL đã chạy: " . $sql);
    }

    // Xử lý kết quả
    $brands_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $brands_data[] = [
                'id' => $row['id'],
                'brand_name' => $row['brand_name'],
                'image' => $row['url'] ?? '../default_brand.webp',
            ];
        }
    }

    return $brands_data;
}

function getMakeupProducts($limit = 8)
{
    global $conn;

    // ID của danh mục 'Trang điểm' trong bảng categories là 46 (dựa trên dữ liệu đã xác nhận)
    $makeup_category_id = 46;

    // Truy vấn SQL chỉ lấy sản phẩm có subcategory_id = 46
    $sql = "SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.description, 
                p.stock, 
                b.brand_name AS brand_name,
                pi.url 
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN (
                -- Truy vấn phụ để lấy ảnh đại diện (url nhỏ nhất)
                SELECT 
                    product_id, 
                    MIN(url) AS url 
                FROM productimage 
                GROUP BY product_id
            ) pi ON p.id = pi.product_id
            -- Điều kiện WHERE để lọc theo danh mục 'Trang điểm' (ID 46)
            WHERE p.subcategory_id = " . (int) $makeup_category_id . "
            ORDER BY p.id DESC 
            LIMIT " . (int) $limit;

    $result = $conn->query($sql);

    // Kiểm tra lỗi (Quan trọng)
    if ($result === false) {
        // Nên log lỗi thay vì die trong môi trường production
        die("LỖI TRUY VẤN: " . $conn->error . "<br>SQL đã chạy: " . $sql);
    }

    $products_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products_data[] = [
                'id' => $row['id'],
                'image' => $row['url'] ?? '../default.webp',
                'name' => $row['name'],
                'brand' => $row['brand_name'] ?? 'Chưa xác định',
                'price' => $row['price'],
                // Giả định hàm formatPrice đã tồn tại
                'formatted_price' => formatPrice($row['price']),
                // ... Thêm các trường dữ liệu khác nếu cần
            ];
        }
    }

    return $products_data;
}

function getLipstickProducts($limit = 8)
{
    global $conn;
    // ID của 'Son môi' (55)
    $lipstick_category_id = 55;

    $sql = "SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.description, 
                p.stock, 
                b.brand_name AS brand_name,
                pi.url 
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN (
                SELECT 
                    product_id, 
                    MIN(url) AS url 
                FROM productimage 
                GROUP BY product_id
            ) pi ON p.id = pi.product_id
            -- Lọc theo ID danh mục 'Son môi'
            WHERE p.subcategory_id = " . (int) $lipstick_category_id . "
            ORDER BY p.id DESC 
            LIMIT " . (int) $limit;

    $result = $conn->query($sql);

    if ($result === false) {
        die("LỖI TRUY VẤN: " . $conn->error . "<br>SQL đã chạy: " . $sql);
    }

    $products_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products_data[] = [
                'id' => $row['id'],
                'image' => $row['url'] ?? '../default.webp',
                'name' => $row['name'],
                'brand' => $row['brand_name'] ?? 'Chưa xác định',
                'price' => $row['price'],
                'formatted_price' => formatPrice($row['price']),
            ];
        }
    }

    return $products_data;
}

function getSkincareProducts($limit = 8)
{
    global $conn;

    // ID của 'Chăm sóc da mặt' (47) và 'Chăm sóc cơ thể' (48)
    $skincare_category_ids = [47, 48];
    // Chuyển mảng ID thành chuỗi ngăn cách bằng dấu phẩy để dùng trong mệnh đề IN
    $ids_string = implode(',', array_map('intval', $skincare_category_ids));

    $sql = "SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.description, 
                p.stock, 
                b.brand_name AS brand_name,
                pi.url 
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN (
                SELECT 
                    product_id, 
                    MIN(url) AS url 
                FROM productimage 
                GROUP BY product_id
            ) pi ON p.id = pi.product_id
            -- Lọc theo các ID danh mục chăm sóc da
            WHERE p.subcategory_id IN (" . $ids_string . ")
            ORDER BY p.id DESC 
            LIMIT " . (int) $limit;

    $result = $conn->query($sql);

    if ($result === false) {
        die("LỖI TRUY VẤN: " . $conn->error . "<br>SQL đã chạy: " . $sql);
    }

    $products_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products_data[] = [
                'id' => $row['id'],
                'image' => $row['url'] ?? '../default.webp',
                'name' => $row['name'],
                'brand' => $row['brand_name'] ?? 'Chưa xác định',
                'price' => $row['price'],
                'formatted_price' => formatPrice($row['price']),
            ];
        }
    }

    return $products_data;
}
