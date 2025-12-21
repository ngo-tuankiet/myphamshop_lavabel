<template>
    <div>
        <h1 style="text-align: center; font-size: 50px;">DANH SÁCH SẢN PHẨM YÊU THÍCH</h1>

        <a-row :gutter="24">
            <a-col span="6" v-for="product in favouriteProducts" :key="product.id">
                <a-card class="custom-card" hoverable style="margin-top: 10px;">
                    
                    <div class="imgProduct">
                        <img :src="product.image" alt="Image" />
                    </div>

                    <div class="content">
                        <p class="name">{{ product.name }}</p>
                        <p class="price">{{ product.formatted_price }}</p>

                        <a-button type="primary" @click="showModal(product)">
                            Thêm vào giỏ hàng
                        </a-button>

                        <div class="like-button-wrapper">
                            <a-button class="like-button" @click="removeFavourite(product.id)">
                                <HeartOutlined style="color: red" />
                            </a-button>
                        </div>
                    </div>

                </a-card>
            </a-col>
        </a-row>

        <a-modal :visible="isModalVisible" title="Thông tin sản phẩm" @cancel="handleCancel" @ok="addToCart">
            <img :src="selectedProduct.image" style="max-width:100%" />
            <h3>{{ selectedProduct.name }}</h3>
            <p class="price">{{ selectedProduct.formatted_price }}</p>

            <div class="quantity-control">
                <a-button @click="decrementQuantity" :disabled="quantity <= 1">-</a-button>
                <span class="quantity">{{ quantity }}</span>
                <a-button @click="incrementQuantity">+</a-button>
            </div>
        </a-modal>
    </div>
</template>

<script>
import { HeartOutlined } from "@ant-design/icons-vue";
import axios from "axios";
import { message } from "ant-design-vue";
import { useRuntimeConfig } from "#app";

export default {
    components: { HeartOutlined },

    data() {
        return {
            apiBase: "",
            favouriteProducts: [],
            isModalVisible: false,
            selectedProduct: {},
            quantity: 1,
        };
    },

    mounted() {
        if (process.client) {
            const config = useRuntimeConfig();
            this.apiBase = config.public.apiBase;
            this.fetchFavouriteProducts();
        }
    },

    methods: {
        async fetchFavouriteProducts() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.warning("Vui lòng đăng nhập");

            try {
                const res = await axios.get(`${this.apiBase}/api/favourites`, {
                    params: { user_id: user.id }
                });

                this.favouriteProducts = res.data.data;
            } catch (err) {
                message.error("Không thể tải danh sách yêu thích");
            }
        },

        async removeFavourite(productId) {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.warning("Vui lòng đăng nhập");

            try {
                await axios.delete(`${this.apiBase}/api/favourites/remove`, {
                    data: {
                        user_id: user.id,
                        product_id: productId
                    }
                });

                this.favouriteProducts = this.favouriteProducts.filter(p => p.id !== productId);
                message.success("Đã xóa khỏi yêu thích");

            } catch (err) {
                message.error("Không thể xóa yêu thích");
            }
        },

        showModal(product) {
            this.selectedProduct = product;
            this.isModalVisible = true;
        },

        handleCancel() {
            this.isModalVisible = false;
            this.quantity = 1;
        },

        async addToCart() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.warning("Vui lòng đăng nhập");

            try {
                await axios.post(`${this.apiBase}/api/cart/add`, {
                    user_id: user.id,
                    product_id: this.selectedProduct.id,
                    quantity: this.quantity
                });

                message.success("Đã thêm vào giỏ hàng!");
                this.isModalVisible = false;

            } catch (err) {
                message.error("Không thể thêm vào giỏ hàng");
            }
        },

        incrementQuantity() { this.quantity++; },
        decrementQuantity() { if (this.quantity > 1) this.quantity--; },
    }
};
</script>

<style scoped>
.custom-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 400px;
    text-align: center;
}

.imgProduct {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

img {
    width: 60%;
    height: 100%;
    object-fit: contain;
}

.price { color: red; font-weight: bold; }

.like-button-wrapper {
    position: absolute;
    top: 10px;
    right: 10px;
}

.like-button {
    width: 30px;
    height: 30px;
    padding: 0 !important;
}

.quantity-control {
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity {
    margin: 0 10px;
}
</style>
