<template>
    <div>
        <h1 style="text-align: center; font-size: 50px;">DANH SÁCH SẢN PHẨM</h1>

        <a-row :gutter="24">
            <a-col span="6" v-for="product in products" :key="product.id">
                <a-card class="custom-card" hoverable style="margin-top: 10px;">
                    <div class="imgProduct">
                        <img :src="product.images?.[0]?.url" alt="Cover Image" />
                    </div>

                    <div class="content">
                        <p class="name">{{ product.name }}</p>
                        <p class="price">{{ product.formatted_price ?? product.price }}</p>

                        <a-button type="primary" @click="showModal(product)">Thêm vào giỏ hàng</a-button>

                        <div class="like-button-wrapper">
                            <a-button class="like-button" @click="toggleLike(product.id)">
                                <template #icon>
                                    <HeartFilled
                                        :style="{ color: likedProducts.includes(product.id) ? 'red' : 'inherit' }"
                                    />
                                </template>
                            </a-button>
                        </div>
                    </div>
                </a-card>
            </a-col>
        </a-row>

        <!-- MODAL -->
        <a-modal :visible="isModalVisible" title="Thông tin sản phẩm"
                 @cancel="handleCancel" @ok="addToCart">
            <img :src="selectedProduct.images?.[0]?.url" style="max-width: 100%;" />

            <h3>{{ selectedProduct.name }}</h3>
            <p class="price">{{ selectedProduct.formatted_price ?? selectedProduct.price }}</p>

            <div class="quantity-control">
                <a-button @click="decrementQuantity" :disabled="quantity <= 1">-</a-button>
                <span class="quantity">{{ quantity }}</span>
                <a-button @click="incrementQuantity">+</a-button>
            </div>
        </a-modal>
    </div>
</template>

<script>
import { MinusOutlined, PlusOutlined, HeartFilled } from '@ant-design/icons-vue';
import { message } from "ant-design-vue";
import axios from "axios";

export default {
    data() {
        return {
            products: [],
            selectedProduct: {},
            isModalVisible: false,
            quantity: 1,
            page: 1,
            limit: 10,
            totalProducts: 0,
            likedProducts: []
        };
    },

    components: {
        MinusOutlined,
        HeartFilled,
        PlusOutlined,
    },

    methods: {
        // ========================= GET PRODUCTS BY CATEGORY =========================
        async fetchProducts() {
    const { idCategory } = this.$route.params;

    try {
        const response = await axios.get("http://localhost:8000/api/user/products", {
            params: {
                category_id: idCategory,
                page: this.page,
                limit: this.limit
            }
        });

        this.products = response.data.data.data;  // mảng sản phẩm
        this.totalProducts = response.data.data.total;

    } catch (error) {
        console.error("Error fetching products:", error);
        message.error("Không thể tải danh sách sản phẩm");
    }},



        // ========================= MODAL =========================
        showModal(product) {
            this.selectedProduct = product;
            this.isModalVisible = true;
        },

        handleCancel() {
            this.isModalVisible = false;
            this.quantity = 1;
        },

        // ========================= ADD TO CART =========================
       async addToCart() {
    try {
        const user = JSON.parse(sessionStorage.getItem("user"));
        if (!user) {
            return message.warning("Vui lòng đăng nhập để thêm vào giỏ hàng");
        }

        const payload = {
            user_id: user.id,
            product_id: this.selectedProduct.id,
            quantity: this.quantity
        };

        const response = await axios.post("http://localhost:8000/api/cart/add", payload);

        if (response.data.success) {
            message.success("Đã thêm sản phẩm vào giỏ hàng");
        } else {
            message.error("Không thể thêm vào giỏ hàng");
        }

        this.isModalVisible = false;
        this.quantity = 1;

    } catch (error) {
        console.error("Add to Cart Error:", error);
        message.error("Lỗi khi thêm vào giỏ hàng");
    }
},

        // ========================= LIKE PRODUCTS =========================
        async toggleLike(id) {
            try {
                const user = JSON.parse(sessionStorage.getItem("user"));
                if (!user) return message.warning("Vui lòng đăng nhập");

                const isLiked = this.likedProducts.includes(id);
                const method = isLiked ? "DELETE" : "POST";

                const res = await fetch(`http://localhost:8000/api/favourites/${id}`, { method });

                if (res.ok) {
                    if (isLiked) {
                        this.likedProducts = this.likedProducts.filter(x => x !== id);
                    } else {
                        this.likedProducts.push(id);
                    }
                    message.success(isLiked ? "Đã xóa yêu thích" : "Đã thêm vào yêu thích");
                }

            } catch (error) {
                console.error(error);
                message.error("Không thể cập nhật yêu thích");
            }
        },

        incrementQuantity() { this.quantity++; },
        decrementQuantity() { if (this.quantity > 1) this.quantity--; }
    },

    mounted() {
        this.fetchProducts();
    },

    watch: {
        '$route.params': 'fetchProducts'
    }
};
</script>

<style scoped>
.custom-card {
    height: 400px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
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

.price {
    color: red;
    font-weight: bold;
}

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
    justify-content: center;
    align-items: center;
}
</style>
