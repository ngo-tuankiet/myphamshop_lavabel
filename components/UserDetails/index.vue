<template>
    <div class="user-details">
        <a-form layout="vertical">
            <a-form-item label="Tên khách hàng">
                <a-input v-model:value="user.fullname" />
            </a-form-item>
            <a-form-item label="Email">
                <a-input v-model:value="user.email" disabled />
            </a-form-item>
            <!-- <a-form-item label="Số điện thoại">
                <a-input v-model:value="user.phone" />
            </a-form-item> -->
            <a-button type="primary" @click="updateInfo">Cập nhật</a-button>
        </a-form>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { message } from 'ant-design-vue'

const user = ref({
    name: '',
    email: '',
    phone: '',
    id: ''
})

onMounted(() => {
    const storedUser = sessionStorage.getItem("user")
    if (storedUser) {
        user.value = JSON.parse(storedUser)
        console.log("hihi:", user.value)
    }
})

const updateInfo = async () => {
    try {
        const payload = {
            id: user.value.id,
            name: user.value.fullname,
        }
        console.log("Hiii", payload)
        const response = await fetch('http://localhost:5000/api/users/update', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        })

        if (response.ok) {
            const result = await response.json()
            message.success(result.message)
            sessionStorage.setItem("user", JSON.stringify(user.value))
        } else {
            const error = await response.json()
            message.error(error.message || 'Cập nhật thông tin thất bại!')
        }
    } catch (error) {
        console.error(error)
        message.error('Có lỗi xảy ra! Vui lòng thử lại.')
    }
}
</script>

<style scoped>
.user-details {
    max-width: 400px;
    margin: auto;
}
</style>