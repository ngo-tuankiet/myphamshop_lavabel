<template>
    <div class="register-page">
        <div class="register-form">
            <h2>ĐĂNG NHẬP</h2>

            <a-form @submit.prevent="onSubmit">
                <a-form-item>
                    <a-input v-model:value="email" placeholder="Email" />
                </a-form-item>

                <a-form-item>
                    <a-input-password v-model:value="password" placeholder="Mật khẩu" />
                </a-form-item>

                <a-button type="primary" style="background-color: black;" html-type="submit" block>
                    Đăng Nhập
                </a-button>

                <div class="login-option">
                    <span>Chưa có tài khoản?</span>
                    <nuxt-link to="/register">Đăng Ký Ngay</nuxt-link>
                </div>
            </a-form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { message } from "ant-design-vue";
const router = useRouter();

const email = ref('');
const password = ref('');

const onSubmit = async () => {

    if (!email.value || !password.value) {
        message.error("Vui lòng nhập đầy đủ thông tin!");
        return;
    }

    try {
        const res = await $fetch('http://127.0.0.1:8000/api/login', {
            method: 'POST',
            body: {
                email: email.value,
                password: password.value
            }
        });

        if (!res.success) {
            message.error(res.message);
            return;
        }

        const user = res.data;   // ✔ user thật từ API

        // LƯU ĐÚNG USER
        sessionStorage.setItem("user", JSON.stringify(user));

        message.success("Đăng nhập thành công!");

        if (user.role === 2) router.push('/admin');
        else router.push('/');

    } catch (err) {
        console.error(err);
        message.error("Đăng nhập thất bại!");
    }
};


onMounted(() => {
    const user = JSON.parse(sessionStorage.getItem("user") || 'null');
    if (user) router.push("/");
});
</script>type "d:\web\frontend\pages\login.vue"

<style scoped>
.register-page {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-size: cover;
}

.register-form {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 30px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.login-option {
    margin-top: 15px;
    text-align: center;
}

.login-option span {
    margin-right: 5px;
}
</style>