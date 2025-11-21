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
echo "Kết nối CSDL thành công!";

// Sau khi sử dụng xong, đóng kết nối
$conn->close();

?>