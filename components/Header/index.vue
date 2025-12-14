<template>
    <a-layout-header
        style="display: flex; align-items: center; justify-content: space-between; background-color:#2e6655">

        <div class="logo"></div>

        <a-menu mode="horizontal" v-model:selectedKeys="selectedKeys"
                style="flex: 1; background-color:#2e6655; line-height: 64px;">

            <!-- Trang chủ -->
            <a-menu-item key="1">
                <nuxt-link to="/" style="color: #fff;">Trang chủ</nuxt-link>
            </a-menu-item>

            <!-- Sản phẩm -->
            <a-sub-menu key="2" @titleClick="loadCategories">
                <template #title>
                    <span style="color: #fff;">Sản phẩm</span>
                </template>

                <template v-if="categories.length > 0">
                    <a-menu-item
                        v-for="category in categories"
                        :key="'cat-' + category.id"
                        @click="navigateToCategory(category.id)"
                    >
                        {{ category.name }}
                    </a-menu-item>
                </template>
            </a-sub-menu>

            <!-- Thương hiệu -->
            <a-menu-item key="3">
                <nuxt-link to="/brands" style="color: #fff;">Thương hiệu</nuxt-link>
            </a-menu-item>

            <!-- Giới thiệu -->
            <a-menu-item key="4">
                <nuxt-link to="/intro" style="color: #fff;">Giới thiệu</nuxt-link>
            </a-menu-item>

            <!-- Liên hệ -->
            <a-menu-item key="5">
                <nuxt-link to="/contact" style="color: #fff;">Liên hệ</nuxt-link>
            </a-menu-item>

        </a-menu>

        <!-- Menu bên phải -->
        <div class="right-menu">
            <SearchOutlined style="font-size: 30px; color: #fff;margin-right: 10px;" />

            <a-dropdown>
                <UserOutlined style="font-size: 30px; color: #fff;margin-right: 10px;" />
                <template #overlay>
                    <a-menu>
                        <a-menu-item @click="handleUserClick">
                            <UserOutlined /> Thông tin cá nhân
                        </a-menu-item>

                        <a-menu-item v-if="isCheck" @click="handleLogout">
                            <LogoutOutlined /> Đăng xuất
                        </a-menu-item>

                        <a-menu-item v-else @click="handleLogin">
                            <LogoutOutlined /> Đăng nhập
                        </a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>

            <ShoppingCartOutlined
                @click="handleToCart"
                style="font-size: 30px; color: #fff;margin-right: 10px;" />
        </div>

    </a-layout-header>
</template>


<script>
import {
    SearchOutlined,
    UserOutlined,
    ShoppingCartOutlined,
    LogoutOutlined
} from '@ant-design/icons-vue';

import axios from 'axios';
import { ref, onMounted } from 'vue';
import { message } from "ant-design-vue";

export default {
    name: "Header",
    components: {
        SearchOutlined,
        UserOutlined,
        ShoppingCartOutlined,
        LogoutOutlined
    },

    setup() {
        const categories = ref([]);
        const isLoading = ref(false);
        const selectedKeys = ref(["1"]);
        const isCheck = ref(false);

        /* ======================= CHECK LOGIN ======================= */
        const checkLoginStatus = () => {
            isCheck.value = !!sessionStorage.getItem("user");
        };


        /* ======================= LOAD CATEGORY MENU ======================= */
        const loadCategories = async () => {
            if (!isLoading.value && categories.value.length === 0) {
                isLoading.value = true;

                try {
                    const res = await axios.get("http://localhost:8000/api/user/categories");
                    categories.value = res.data.data; // BE return đúng dạng này
                } catch (error) {
                    console.error("Error loading categories:", error);
                } finally {
                    isLoading.value = false;
                }
            }
        };


        /* ======================= MENU ACTIVE ======================= */
        const updateSelectedKey = () => {
            const path = window.location.pathname;

            if (path === "/") selectedKeys.value = ["1"];

            // Sản phẩm /product/:id
            else if (path.startsWith("/product")) selectedKeys.value = ["2"];

            // Thương hiệu /brands hoặc /brandsnew/:id
            else if (path.startsWith("/brands") || path.startsWith("/brandsnew"))
                selectedKeys.value = ["3"];

            // Giới thiệu
            else if (path.startsWith("/intro")) selectedKeys.value = ["4"];

            // Liên hệ
            else if (path.startsWith("/contact")) selectedKeys.value = ["5"];
        };


        /* ======================= LOGOUT ======================= */
        const handleLogout = () => {
            sessionStorage.removeItem("user");
            message.success("Đăng xuất thành công!");
            window.location.href = "/";
        };


        /* ======================= USER PROFILE ======================= */
        const handleUserClick = () => {
            const user = sessionStorage.getItem("user");
            window.location.href = user ? "/infor" : "/login";
        };


        /* ======================= CATEGORY NAVIGATE ======================= */
        const navigateToCategory = (id) => {
            window.location.href = `/product/${id}`;
        };


        /* ======================= CART ======================= */
        const handleToCart = () => {
            const user = sessionStorage.getItem("user");

            if (user) window.location.href = "/cart";
            else message.warning("Vui lòng đăng nhập để vào giỏ hàng");
        };


        const handleLogin = () => (window.location.href = "/login");


        /* ======================= ON MOUNT ======================= */
        onMounted(() => {
            checkLoginStatus();
            updateSelectedKey();
        });


        return {
            categories,
            selectedKeys,
            isCheck,
            loadCategories,
            navigateToCategory,
            handleUserClick,
            handleToCart,
            handleLogout,
            handleLogin
        };
    }
};
</script>


<style scoped>

</style>
