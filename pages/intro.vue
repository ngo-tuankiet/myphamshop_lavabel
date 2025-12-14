<template>
    <div class="intro-container">
        <!-- Header Section -->
        <div class="intro-header">
            <h1>Giới thiệu Mypham.vn</h1>
            <ul>
                <li>Mypham.vn được thành lập tại Việt Nam vào tháng 4/2016 với mục tiêu chăm sóc sắc đẹp và sức khỏe
                    toàn diện cho người Việt Nam.</li>
                <li>Mypham.vn đã tạo ra những trải nghiệm mua sắm trực tuyến tuyệt vời cùng dịch vụ chăm sóc Spa chuyên
                    nghiệp với các thiết bị hiện đại hàng đầu thế giới hiện nay.</li>
            </ul>
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <!-- Hàng chính hãng -->
            <div class="feature-card">
                <div class="icon-wrapper">
                    <img src="https://hotro.Mypham.vn/files/graphics/icon_hangchinhang.svg" alt="Hàng chính hãng" />
                </div>
                <h3>Cam kết cung cấp các sản phẩm chính hãng</h3>
                <p>Cam kết cung cấp các sản phẩm chính hãng và nhập khẩu 100% từ Mỹ, Pháp. Số lượng sản phẩm và dịch vụ
                    lớn nhất với chủng loại đa dạng.</p>
            </div>

            <!-- Bảo mật -->
            <div class="feature-card">
                <div class="icon-wrapper">
                    <img src="https://hotro.Mypham.vn/files/graphics/icon_safe.svg" alt="Bảo mật" />
                </div>
                <h3>Cam kết bảo mật</h3>
                <p>Cam kết bảo mật thông tin khách hàng.</p>
            </div>

            <!-- Tư vấn -->
            <div class="feature-card">
                <div class="icon-wrapper">
                    <img src="https://hotro.Mypham.vn/files/banner/icon_doitramienphi_30ngay.svg" alt="Tư vấn" />
                </div>
                <h3>Cam kết tư vấn chuyên nghiệp</h3>
                <p>Cam kết tư vấn và hỗ trợ quý khách kịp thời, nhiệt tình theo đúng các quy định của pháp luật và chuẩn
                    mực đạo đức nghề nghiệp.</p>
            </div>

            <!-- Giao hàng -->
            <div class="feature-card">
                <div class="icon-wrapper">
                    <img src="https://hotro.Mypham.vn/files/graphics/icon_hks_nowfree.svg" alt="Giao hàng" />
                </div>
                <h3>Giao hàng nhanh</h3>
                <p>Cam kết vận hành tốc độ giao hàng nhanh vượt trội nhằm rút ngắn tối đa thời gian khách hàng nhận được
                    sản phẩm.</p>
            </div>
        </div>

        <!-- Shopping Experience -->
        <div class="shopping-experience">
            <!-- <div class="shopping-image">
                <img src="/images/shopping.svg" alt="Online Shopping" />
            </div> -->
            <div class="shopping-content">
                <h2>Trải nghiệm mua sắm trực tuyến</h2>
                <p>Đến với Mypham.vn khách hàng sẽ trải nghiệm việc mua sắm trực tuyến với các bước
                    <span class="highlight">thanh toán an toàn, đơn giản, nhanh chóng</span>, đáp ứng tiêu chuẩn Quốc
                    tế.
                </p>
                <p>Với phương châm <span class="motto">"Chất lượng thật - Giá trị thật"</span>
                    Mypham.vn luôn nỗ lực không ngừng nhằm nâng cao chất lượng dịch vụ để khách hàng được hưởng các dịch
                    vụ chăm sóc tốt nhất.</p>
            </div>
        </div>

        <!-- Branch Locations -->
        <!-- <div class="branches-section">
            <h2>Chi nhánh Mypham</h2>
            <div class="branches-grid">
                <div v-for="(branch, index) in 3" :key="index" class="branch-card">
                    <img :src="`/images/branch${index + 1}.jpg`" :alt="`Chi nhánh Mypham ${index + 1}`" />
                </div>
            </div>
        </div> -->
    </div>
</template>

<style scoped>
.intro-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.intro-header {
    background-color: #f0f8f0;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.intro-header h1 {
    font-size: 2rem;
    font-weight: bold;
    color: #2c5282;
    margin-bottom: 1rem;
}

.intro-header ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    color: #2f855a;
}

.intro-header li {
    margin-bottom: 0.5rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.feature-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.icon-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

.icon-wrapper img {
    width: 64px;
    height: 64px;
}

.feature-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2d3748;
}

.shopping-experience {
    display: flex;
    gap: 2rem;
    align-items: center;
    background-color: #f7fafc;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 3rem;
}

.shopping-image {
    flex: 1;
}

.shopping-image img {
    width: 100%;
    max-width: 400px;
}

.shopping-content {
    flex: 2;
}

.shopping-content h2 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.highlight {
    font-weight: 600;
}

.motto {
    color: #2f855a;
    font-weight: bold;
}

.branches-section {
    text-align: center;
}

.branches-section h2 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 2rem;
}

.branches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.branch-card {
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.branch-card:hover {
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.branch-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

@media (max-width: 768px) {
    .shopping-experience {
        flex-direction: column;
    }

    .shopping-image {
        text-align: center;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .intro-container {
        padding: 1rem;
    }

    .intro-header {
        padding: 1rem;
    }

    .intro-header h1 {
        font-size: 1.5rem;
    }
}
</style>

<script setup>
// Các computed properties hoặc methods có thể được thêm vào đây nếu cần
</script>