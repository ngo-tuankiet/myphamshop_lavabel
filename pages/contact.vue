<template>
    <div class="contact-form-container">
        <div class="contact-form">
            <h2>Liên hệ với chúng tôi</h2>
            <form @submit.prevent="submitForm">
                <div>
                    <label for="name">Họ và tên:</label>
                    <input id="name" v-model="name" type="text" required />
                </div>
                <div>
                    <label for="phone">Số điện thoại:</label>
                    <input id="phone" v-model="phone" type="tel" required />
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input id="email" v-model="email" type="email" required />
                </div>
                <div>
                    <label for="message">Nội dung:</label>
                    <textarea id="message" v-model="message" required></textarea>
                </div>
                <a-button type="primary" style="background-color: black;" html-type="submit" block>
                    Gửi Đi
                </a-button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            name: '',
            phone: '',
            email: '',
            message: ''
        }
    },

}
</script>

<style scoped>
.contact-form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.contact-form {
    background-color: #fff;
    padding: 32px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    max-width: 500px;
    width: 100%;
}

.contact-form h2 {
    text-align: center;
    margin-bottom: 24px;
}

.contact-form form div {
    margin-bottom: 16px;
}

.contact-form label {
    display: block;
    margin-bottom: 4px;
    font-weight: 500;
}

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d9d9d9;
    border-radius: 4px;
    font-size: 14px;
}

.contact-form button {
    display: block;
    margin: 0 auto;
}
</style>