<template>
    <a-layout style="min-height: 100vh">
        <!-- Sidebar -->
        <a-layout-sider v-model:collapsed="collapsed" collapsible class="sidebar">
            <div class="logo">Admin Panel</div>
            <a-menu theme="dark" mode="inline" :selectedKeys="selectedKeys">
                <a-menu-item key="product">
                    <router-link to="/admin/product">
                        <desktop-outlined />
                        <span>Quản lý sản phẩm</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="brands">
                    <router-link to="/admin/brands">
                        <shopping-outlined />
                        <span>Quản lý thương hiệu</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="orders">
                    <router-link to="/admin/orders">
                        <shopping-outlined />
                        <span>Quản lý đơn hàng</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="categories">
                    <router-link to="/admin/categories">
                        <folder-outlined />
                        <span>Quản lý loại sản phẩm</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="user">
                    <router-link to="/admin/user">
                        <folder-outlined />
                        <span>Quản lý tài khoản</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="promotion">
                    <router-link to="/admin/promotion">
                        <folder-outlined />
                        <span>Quản lý mã khuyến mại</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="contact">
                    <router-link to="/admin/contact">
                        <folder-outlined />
                        <span>Quản lý liên hệ</span>
                    </router-link>
                </a-menu-item>
                <a-menu-item key="logout" @click="handleLogout">
                    <logout-outlined />
                    <span>Đăng xuất</span>
                </a-menu-item>
            </a-menu>
        </a-layout-sider>

        <!-- Main Content -->
        <a-layout>
            <a-layout-header style="background: #fff; padding: 0">
                <a-breadcrumb style="margin: 16px 24px">
                    <a-breadcrumb-item>Admin</a-breadcrumb-item>
                    <a-breadcrumb-item>{{ currentPage }}</a-breadcrumb-item>
                </a-breadcrumb>
            </a-layout-header>

            <a-layout-content style="margin: 24px 16px 0">
                <div :style="{ padding: '24px', background: '#fff', minHeight: '360px' }">
                    <NuxtPage />
                </div>
            </a-layout-content>

            <a-layout-footer style="text-align: center">
                Admin Dashboard ©{{ new Date().getFullYear() }}
            </a-layout-footer>
        </a-layout>
    </a-layout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
    DesktopOutlined,
    ShoppingOutlined,
    FolderOutlined,
    LogoutOutlined
} from '@ant-design/icons-vue'

const route = useRoute()
const router = useRouter()
const collapsed = ref(false)

// Computed property for selected menu item
const selectedKeys = computed(() => {
    const path = route.path
    const key = path.split('/').pop()
    return [key]
})

// Computed property for current page name
const currentPage = computed(() => {
    const key = selectedKeys.value[0]
    switch (key) {
        case 'product':
            return 'Quản lý sản phẩm'
        case 'brands':
            return 'Quản lý thương hiệu'
        case 'orders':
            return 'Quản lý đơn hàng'
        case 'user':
            return 'Quản lý tài khoản'
        case 'categories':
            return 'Quản lý loại sản phẩm'
        case 'promotion':
            return 'Quản lý mã khuyến mại'
        case 'contact':
            return 'Quản lý liên hệ'
        default:
            return ''
    }
})

// Logout handler
const handleLogout = () => {
    localStorage.removeItem('token')
    router.push('/login')
}
</script>

<style scoped>
.sidebar {
    position: fixed;
    left: 0;
    height: 100vh;
    z-index: 10;
}

.logo {
    height: 32px;
    margin: 16px;
    color: white;
    font-size: 18px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
}

:deep(.ant-layout) {
    margin-left: 200px;
    transition: margin-left 0.2s;
}

:deep(.ant-layout-sider-collapsed)+.ant-layout {
    margin-left: 80px;
}
</style>