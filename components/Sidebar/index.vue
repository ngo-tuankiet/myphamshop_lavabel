<template>
    <a-layout-sider width="250" theme="light">
        <a-menu mode="inline" v-model:selectedKeys="selectedKey">

            <a-menu-item key="1">
                <UserOutlined />
                <NuxtLink to="/profile">Thông tin chi tiết</NuxtLink>
            </a-menu-item>

            <a-menu-item key="2">
                <HeartOutlined />
                <NuxtLink to="/wishlist">Yêu thích</NuxtLink>
            </a-menu-item>

            <a-menu-item key="3">
                <ShoppingCartOutlined />
                <NuxtLink to="/orders">Đơn hàng</NuxtLink>
            </a-menu-item>

            <a-menu-item key="4">
                <LockOutlined />
                <NuxtLink to="/change-password">Cập nhật mật khẩu</NuxtLink>
            </a-menu-item>

            <a-menu-item key="5" @click="logout">
                <LogoutOutlined />
                <span>Đăng xuất</span>
            </a-menu-item>

        </a-menu>
    </a-layout-sider>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { message } from "ant-design-vue";

import { 
    UserOutlined,
    HeartOutlined,
    ShoppingCartOutlined,
    LockOutlined,
    LogoutOutlined
} from "@ant-design/icons-vue";

// MUST be inside <script setup>
const router = useRouter();

const selectedKey = ref(["1"]);

const logout = () => {
    sessionStorage.removeItem("user");
    message.success("Đăng xuất thành công!");
    router.push("/login");
};
</script>

<style scoped>
</style>
