<template>
    <div>
        <a-card class="w-full max-w-md">
            <a-card-header>
                <a-card-title>Change Password</a-card-title>
            </a-card-header>

            <a-card-content>
                <div class="space-y-4">

                    <a-label>Mật khẩu cũ</a-label>
                    <a-input type="password" v-model:value="oldPassword" />

                    <a-label>Mật khẩu mới</a-label>
                    <a-input type="password" v-model:value="newPassword" />

                    <a-label>Xác nhận mật khẩu</a-label>
                    <a-input type="password" v-model:value="confirmPassword" />

                    <a-button type="primary" @click="handlePasswordChange">
                        Update Password
                    </a-button>
                </div>
            </a-card-content>
        </a-card>
    </div>
</template>

<script setup>
import { ref } from "vue"
import { message } from "ant-design-vue"

const oldPassword = ref("")
const newPassword = ref("")
const confirmPassword = ref("")

const handlePasswordChange = async () => {
    const user = JSON.parse(sessionStorage.getItem("user") || "null")

    if (!user) {
        return message.error("Bạn chưa đăng nhập")
    }

    if (newPassword.value !== confirmPassword.value) {
        return message.error("Mật khẩu xác nhận không khớp")
    }

    try {
        const res = await $fetch("http://127.0.0.1:8000/api/change-password", {
            method: "POST",
            body: {
                user_id: user.id,
                old_password: oldPassword.value,
                new_password: newPassword.value
            }
        })

        if (res.success) {
            message.success("Đổi mật khẩu thành công")
            oldPassword.value = ""
            newPassword.value = ""
            confirmPassword.value = ""
        } else {
            message.error(res.message || "Đổi mật khẩu thất bại")
        }

    } catch (err) {
        message.error("Lỗi server!")
    }
}
</script>
