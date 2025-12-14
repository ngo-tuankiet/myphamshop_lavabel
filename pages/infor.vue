<template>
    <div class="user-info-container" style="height: 80vh;">
        <a-card style="width: 100%; height: 100%;">
            <a-tabs default-active-key="1" centered @change="handleTabChange">
                <a-tab-pane key="1" tab="Thông tin chi tiết">
                    <UserDetails />
                </a-tab-pane>
                <a-tab-pane key="2" tab="Yêu thích">
                    <FavoriteItems />
                </a-tab-pane>
                <a-tab-pane key="3" tab="Đơn hàng">
                    <OrdersItems />
                </a-tab-pane>
                <a-tab-pane key="4" tab="Cập nhật mật khẩu">
                    <ChangePassword />
                </a-tab-pane>
                <a-tab-pane key="5" tab="Đăng xuất">
                    <div class="logout-container">
                        <p>Bạn có chắc chắn muốn đăng xuất?</p>
                        <a-button type="primary" danger @click="handleLogout">
                            Đăng xuất
                        </a-button>
                    </div>
                </a-tab-pane>
            </a-tabs>
        </a-card>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { message } from 'ant-design-vue'
import UserDetails from '../components/UserDetails/index.vue'
import FavoriteItems from '../components/Favourite/index.vue'
import OrdersItems from '../components/Orders/index.vue'
import ChangePassword from '../components/ChangePassword/index.vue'
const router = useRouter()

const handleLogout = () => {
    try {
        sessionStorage.removeItem('user')
        message.success('Đăng xuất thành công!')
        router.push('/')
    } catch (error) {
        console.error('Lỗi khi đăng xuất:', error)
        message.error('Có lỗi xảy ra khi đăng xuất!')
    }
}

const handleTabChange = (key) => {
    if (key === '5') {
        handleLogout()
    }
}
</script>

<style scoped>
.user-info-container {
    display: flex;
    justify-content: center;
    margin-top: 50px;
}

.logout-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

.logout-container p {
    margin-bottom: 20px;
}

a-card {
    width: 100%;
}

a-tabs {
    margin-top: 20px;
}
</style>