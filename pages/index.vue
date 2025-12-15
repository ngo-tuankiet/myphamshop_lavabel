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
