<template>
    <div>
        <!-- SLIDESHOW BANNER -->
        <div class="slideshow-container">
            <div class="slide" v-for="(image, index) in images" :key="index" 
                 :class="{ active: index === currentIndex }">
                <img :src="image" alt="Banner" />
            </div>
        </div>

        <!-- PRODUCTS SECTION -->
        <div class="products-section">
            <h1 style="text-align: center; font-size: 40px; margin: 40px 0;">SẢN PHẨM NỔI BẬT</h1>

            <a-row :gutter="24">
                <a-col :span="6" v-for="product in products" :key="product.id">
                    <a-card class="custom-card" hoverable>
                        <div class="imgProduct">
                            <img :src="product.images?.url" alt="Product Image" />
                        </div>

                        <div class="content">
                            <p class="name">{{ product.name }}</p>
                            <p class="price">{{ product.formatted_price ?? product.price }}</p>

                            <a-button type="primary" @click="showModal(product)">
                                Thêm vào giỏ hàng
                            </a-button>

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
        </div>

        <!-- MODAL -->
        <a-modal :visible="isModalVisible" title="Thông tin sản phẩm"
                 @cancel="handleCancel" @ok="addToCart">
            <img :src="selectedProduct.images?.url" style="max-width: 100%;" />

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
import { HeartFilled } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";

export default {
    components: { HeartFilled },

    data() {
        return {
            images: [
                "https://file.hstatic.net/1000391653/file/slider-ct-28---31__1__master.jpg",
                "https://file.hstatic.net/1000391653/file/crazynight-new_1_master.jpg",
                "https://file.hstatic.net/1000391653/file/dbt-new_1_master.jpg",
            ],
            currentIndex: 0,

            products: [],
            selectedProduct: {},
            isModalVisible: false,
            quantity: 1,
            likedProducts: []
        };
    },

    mounted() {
        this.apiBase = useRuntimeConfig().public.apiBase;

        this.startSlideShow();
        this.fetchProducts();
        this.loadFavouriteProducts();
    },

    methods: {
        startSlideShow() {
            setInterval(() => {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
            }, 6000);
        },

        async fetchProducts() {
            try {
                const res = await fetch(
                    `${this.apiBase}/api/home/hotProducts`
                );
                const data = await res.json();
                this.products = data.data ?? data;

            } catch (err) {
                console.error(err);
                message.error("Không tải được sản phẩm!");
            }
        },

        async loadFavouriteProducts() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return;

            try {
                const res = await fetch(
                    `${this.apiBase}/api/favourites?user_id=${user.id}`
                );
                const data = await res.json();
                this.likedProducts = data.data.map(item => item.product_id);

            } catch (error) {
                console.error("Lỗi load favourites:", error);
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
                await fetch(`${this.apiBase}/api/cart/add`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        user_id: user.id,
                        product_id: this.selectedProduct.id,
                        quantity: this.quantity
                    })
                });

                message.success("Đã thêm vào giỏ hàng!");
                this.isModalVisible = false;
                this.quantity = 1;

            } catch (err) {
                console.error(err);
                message.error("Lỗi khi thêm giỏ hàng");
            }
        },

        async toggleLike(productId) {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.warning("Bạn cần đăng nhập");

            const isLiked = this.likedProducts.includes(productId);

            const url = isLiked
                ? `${this.apiBase}/api/favourites/remove`
                : `${this.apiBase}/api/favourites/add`;

            try {
                const res = await fetch(url, {
                    method: isLiked ? "DELETE" : "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        user_id: user.id,
                        product_id: productId
                    })
                });

                if (res.ok) {
                    if (isLiked) {
                        this.likedProducts = this.likedProducts.filter(id => id !== productId);
                        message.success("Đã xóa yêu thích");
                    } else {
                        this.likedProducts.push(productId);
                        message.success("Đã thêm yêu thích");
                    }
                }

            } catch (error) {
                console.error("Like error:", error);
                message.error("Không thể kết nối API yêu thích");
            }
        },

        incrementQuantity() { this.quantity++; },
        decrementQuantity() { if (this.quantity > 1) this.quantity--; },
    }
};
</script>

<style scoped>
/* SLIDESHOW */
.slideshow-container {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    margin-bottom: 40px;
}

.slide {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.slide.active {
    opacity: 1;
}

.slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* PRODUCTS */
.products-section {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.custom-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 420px;
    text-align: center;
    margin-bottom: 20px;
}

.content {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-grow: 1;
}

.imgProduct {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.imgProduct img {
    width: 70%;
    height: 100%;
    object-fit: contain;
}

.name {
    font-size: 16px;
    font-weight: 500;
    margin: 10px 0;
}

.price {
    color: red;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 15px;
}

.like-button-wrapper {
    position: absolute;
    top: 10px;
    right: 10px;
}

.like-button {
    width: 32px;
    height: 32px;
    padding: 0;
}

.quantity-control {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-top: 20px;
}

.quantity {
    font-size: 18px;
    font-weight: bold;
}
</style>