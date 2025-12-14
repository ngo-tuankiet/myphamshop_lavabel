<template>
    <footer class="footer">
        <a-row :gutter="24">
            <a-col xs="24" sm="12" md="6">
                <h3>HỖ TRỢ KHÁCH HÀNG</h3>
                <p><span class="hotline">Hotline: <span class="hotline-number">1800 6324</span></span> (miễn phí ,
                    08-22h kể cả T7, CN)</p>
                <ul>
                    <li>Các câu hỏi thường gặp</li>
                    <li>Gửi yêu cầu hỗ trợ</li>
                    <li>Hướng dẫn đặt hàng</li>
                    <li>Phương thức vận chuyển</li>
                    <li>Chính sách đổi trả</li>
                </ul>
            </a-col>
            <a-col xs="24" sm="12" md="6">
                <h3>VỀ MYPHAM.VN</h3>
                <ul>
                    <li>Giới thiệu Mypham.vn</li>
                    <li>Tuyển Dụng</li>
                    <li>Chính sách bảo mật</li>
                    <li>Điều khoản sử dụng</li>
                    <li>Liên hệ</li>
                </ul>
            </a-col>
            <a-col xs="24" sm="12" md="6">
                <h3>HỢP TÁC & LIÊN KẾT</h3>
                <ul>
                    <li><a href="https://Mypham.vn/clinic">https://Mypham.vn/clinic</a></li>
                    <li>Mypham cẩm nang</li>
                </ul>
                <!-- <div class="social-icons">
                    <img src="path/to/facebook-icon.png" alt="Facebook" />
                    <img src="path/to/tiktok-icon.png" alt="TikTok" />
                    <img src="path/to/instagram-icon.png" alt="Instagram" />
                </div>
                <div class="payment-icons">
                    <img src="path/to/mastercard-icon.png" alt="Mastercard" />
                    <img src="path/to/visa-icon.png" alt="Visa" />
                    <img src="path/to/atm-icon.png" alt="ATM" />
                </div> -->
            </a-col>
            <a-col xs="24" sm="12" md="6">
                <h3>CẬP NHẬT THÔNG TIN KHUYẾN MÃI</h3>
                <div class="newsletter">
                    <input type="email" placeholder="email của bạn" />
                    <button class="subscribe-btn">Đăng ký</button>
                </div>
                <div class="app-download">
                    <!-- <img src="path/to/qrcode-icon.png" alt="QR Code" /> -->
                    <div class="app-links">
                        <p>App Store</p>
                        <p>Google Play</p>
                    </div>
                </div>
            </a-col>
        </a-row>
    </footer>
</template>

<script>
export default {
    name: 'Footer',
};
</script>

<style scoped>
.footer {
    background-color: #2e6655;
    padding: 20px;
    color: white;
}

.footer h3 {
    color: white;
    font-size: 18px;
    margin-bottom: 10px;
}

.footer ul {
    list-style: none;
    padding: 0;
}

.footer ul li {
    margin-bottom: 5px;
    font-size: 14px;
}

.hotline {
    color: orange;
}

.hotline-number {
    font-weight: bold;
}

.social-icons img,
.payment-icons img {
    width: 24px;
    height: 24px;
    margin-right: 10px;
}

.newsletter {
    display: flex;
    margin-top: 10px;
}

.newsletter input {
    flex: 1;
    padding: 5px;
    border: none;
    border-radius: 4px;
}

.subscribe-btn {
    background-color: orange;
    border: none;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    margin-left: 5px;
    cursor: pointer;
}

.app-download {
    display: flex;
    align-items: center;
    margin-top: 10px;
}

.app-download img {
    width: 80px;
    height: 80px;
    margin-right: 10px;
}

.app-links p {
    margin: 0;
    font-size: 14px;
}
</style>
