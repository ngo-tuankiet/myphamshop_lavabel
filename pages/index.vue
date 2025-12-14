<template>
    <div>
        <!-- SLIDER -->
        <div class="slider">
            <div
                v-for="(image, index) in images"
                :key="index"
                class="slide"
                :class="{ active: index === currentIndex }"
            >
                <img :src="image" />
            </div>
        </div>

        <!-- TITLE -->
        <a-row type="flex" justify="center" style="margin-top: 80px;">
            <h1 style="font-size: 50px;">TOP SẢN PHẨM BÁN CHẠY</h1>
        </a-row>

        <!-- PRODUCT LIST -->
        <a-row :gutter="24" style="margin-top: 40px; justify-content: center;">
            <a-col span="6" v-for="product in products" :key="product.id">
                <a-card class="custom-card" hoverable>
                    <div class="imgProduct">
                        <img :src="product.image" />
                    </div>

                    <div class="content">
                        <p class="name">{{ product.name }}</p>
                        <p class="price">{{ product.formatted_price }}</p>

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

        <!-- MODAL -->
        <a-modal
            :visible="isModalVisible"
            title="Thông tin sản phẩm"
            @cancel="handleCancel"
            @ok="addToCart"
        >
            <img
                :src="selectedProduct.image"
                style="width: 100%; max-height: 250px; object-fit: contain"
            />

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
import { HeartFilled } from "@ant-design/icons-vue";
import axios from "axios";
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
            likedProducts: [] // danh sách id sản phẩm đã thích
        };
    },

    mounted() {
        this.startSlideShow();
        this.fetchProducts();
        this.loadFavouriteProducts();
    },

    methods: {
        // ==================== SLIDE SLOW ====================
        startSlideShow() {
            setInterval(() => {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
            }, 6000); // chậm lại
        },

        // ==================== LOAD HOT PRODUCTS ====================
        async fetchProducts() {
            try {
                const res = await axios.get("http://localhost:8000/api/home/hotProducts");
                this.products = res.data.data ?? res.data;

            } catch (err) {
                console.error(err);
                message.error("Không tải được sản phẩm!");
            }
        },

        // ==================== LOAD FAVOURITES ====================
        async loadFavouriteProducts() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return;

            try {
                const res = await axios.get("http://localhost:8000/api/favourites", {
                    params: { user_id: user.id }
                });

                this.likedProducts = res.data.data.map(item => item.product_id);

            } catch (error) {
                console.error("Lỗi load favourites:", error);
            }
        },

        // ==================== MODAL ====================
        showModal(product) {
            this.selectedProduct = product;
            this.isModalVisible = true;
        },

        handleCancel() {
            this.isModalVisible = false;
            this.quantity = 1;
        },

        // ==================== ADD TO CART ====================
        async addToCart() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.warning("Vui lòng đăng nhập để thêm vào giỏ");

            try {
                await axios.post("http://localhost:8000/api/cart/add", {
                    user_id: user.id,
                    product_id: this.selectedProduct.id,
                    quantity: this.quantity
                });

                message.success("Đã thêm vào giỏ hàng!");
                this.isModalVisible = false;
                this.quantity = 1;

            } catch (err) {
                console.error(err);
                message.error("Lỗi khi thêm giỏ hàng");
            }
        },

        // ==================== LIKE / UNLIKE ====================
        async toggleLike(productId) {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.warning("Bạn cần đăng nhập");

            const isLiked = this.likedProducts.includes(productId);

            const url = isLiked
                ? "http://localhost:8000/api/favourites/remove"
                : "http://localhost:8000/api/favourites/add";

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
/* SLIDER */
.slider { position: relative; width: 100%; height: 500px; overflow: hidden; }
.slide img { width: 100%; height: 100%; object-fit: cover; }
.slide { position: absolute; width:100%; height:100%; opacity:0; transition:0.5s; transform:translateX(100%); }
.slide.active { opacity:1; transform:translateX(0); }

/* CARD */
.custom-card { height: 420px; text-align:center; display:flex; flex-direction:column; }
.imgProduct { height:200px; display:flex; align-items:center; justify-content:center; }
img { width:60%; height:100%; object-fit:contain; }
.price { color:red; font-weight:bold; }

.like-button-wrapper { position:absolute; top:10px; right:10px; }
.like-button { width:30px; height:30px; padding:0 !important; }

.quantity-control { display:flex; justify-content:center; align-items:center; }
.quantity { margin:0 10px; }
</style>
