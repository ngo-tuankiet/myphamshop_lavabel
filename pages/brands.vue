<template>
    <div class="brands-container p-6">
        <a-row :gutter="[16, 16]">
            <a-col v-for="brand in brands" :key="brand.id" :xs="24" :sm="12" :md="8" :lg="6" :xl="4">
                <a-card hoverable class="brand-card" @click="navigateToBrandProducts(brand.id)">
                    <a-card-meta :title="brand.brand_name">
                        <template #description>
                            <p>{{ brand.description || 'Không có mô tả' }}</p>
                        </template>
                    </a-card-meta>
                </a-card>
            </a-col>
        </a-row>

        <div v-if="loading" class="loading-container">
            <a-spin size="large" />
        </div>

        <a-alert v-if="error" type="error" :message="error" class="mt-4" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const brands = ref([])
const loading = ref(false)
const error = ref(null)

const fetchBrands = async () => {
    try {
        loading.value = true
        error.value = null

        // ⭐ GỌI API ĐÚNG THEO BE
        const response = await fetch('http://127.0.0.1:8000/api/user/brands')

        if (!response.ok) {
            throw new Error('Failed to fetch brands')
        }

        const res = await response.json()

        // ⭐ BE trả về: { message: "...", data: { data: [ ... ] } }
        brands.value = res.data.data  
    } catch (err) {
        error.value = err.message
        console.error('Error fetching brands:', err)
    } finally {
        loading.value = false
    }
}

const navigateToBrandProducts = (brandId) => {
    window.location.href = `/brandsnew/${brandId}`
}

onMounted(() => {
    fetchBrands()
})
</script>

<style scoped>
.brands-container {
    max-width: 1400px;
    margin: 0 auto;
}

.brand-card {
    height: 100%;
    transition: all 0.3s;
}

.brand-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
