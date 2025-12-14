<template>
    <div>
        <a-layout-content class="content">
            <a-row :gutter="16">
                <a-col :span="6">
                    <a-card>
                        <a-statistic title="Người dùng" :value="stats.totalUsers" :precision="0">
                            <template #prefix>
                                <user-outlined />
                            </template>
                        </a-statistic>
                    </a-card>
                </a-col>
                <a-col :span="6">
                    <a-card>
                        <a-statistic title="Sản phẩm" :value="stats.totalProducts" :precision="0">
                            <template #prefix>
                                <shopping-outlined />
                            </template>
                        </a-statistic>
                    </a-card>
                </a-col>
                <a-col :span="6">
                    <a-card>
                        <a-statistic title="Đơn đặt" :value="stats.totalOrders" :precision="0">
                            <template #prefix>
                                <shopping-cart-outlined />
                            </template>
                        </a-statistic>
                    </a-card>
                </a-col>
                <a-col :span="6">
                    <a-card>
                        <a-statistic title="Tổng thu nhập" :value="stats.totalRevenue" :precision="0" suffix="đ">
                            <template #prefix>
                                <dollar-outlined />
                            </template>
                        </a-statistic>
                    </a-card>
                </a-col>
            </a-row>
        </a-layout-content>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
    UserOutlined,
    ShoppingOutlined,
    ShoppingCartOutlined,
    DollarOutlined
} from '@ant-design/icons-vue'
import { Statistic } from 'ant-design-vue'

// Define the layout for the page
definePageMeta({
    layout: 'admin'
})

// State for stats data
const stats = ref({
    totalOrders: 0,
    totalProducts: 0,
    totalRevenue: 0,
    totalUsers: 0
})

// Fetch stats data from the API
const fetchDashboardStats = async () => {
    try {
        const response = await $fetch('http://localhost:5000/api/order/dashboard')
        stats.value = response
        console.log("Dashboard stats:", stats.value)
    } catch (error) {
        console.error('Error fetching dashboard stats:', error)
    }
}

// Fetch stats when component is mounted
onMounted(() => {
    fetchDashboardStats()
})
</script>

<style scoped>
.sidebar {
    min-height: 100vh;
}

.logo {
    height: 64px;
    padding: 16px;
    text-align: center;
}

.logo img {
    height: 32px;
}

.content {
    padding: 24px;
    min-height: 280px;
}

.mt-4 {
    margin-top: 24px;
}
</style>