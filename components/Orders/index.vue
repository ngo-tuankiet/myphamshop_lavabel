<script>
import { message } from "ant-design-vue";
import { EyeOutlined, CloseCircleOutlined } from "@ant-design/icons-vue";

export default {
    components: { EyeOutlined, CloseCircleOutlined },

    data() {
        return {
            apiBase: "",

            orders: [],
            loading: true,

            modalVisible: false,
            orderDetail: null,

            modalVisibleCancel: false,
            idCancel: null,

            columns: [
                { title: "Mã đơn", dataIndex: "id", key: "id" },
                { title: "Ngày", dataIndex: "created_at", key: "created_at" },
                {
                    title: "Tổng tiền",
                    dataIndex: "total_amount",
                    key: "total_amount",
                    slots: { customRender: "total_amount" }
                },
                {
                    title: "Trạng thái",
                    dataIndex: "status",
                    key: "status",
                    slots: { customRender: "status" }
                },
                {
                    title: "Thao tác",
                    key: "action",
                    width: 120,
                    slots: { customRender: "action" }
                }
            ],

            detailColumns: [
                { title: "Tên sản phẩm", dataIndex: "product_name", key: "product_name" },
                { title: "SL", dataIndex: "quantity", key: "quantity" },
                {
                    title: "Đơn giá",
                    dataIndex: "price",
                    key: "price",
                    slots: { customRender: "price" }
                },
                {
                    title: "Thành tiền",
                    key: "totalPrice",
                    slots: { customRender: "totalPrice" }
                }
            ]
        };
    },

    mounted() {
        const config = useRuntimeConfig();
        this.apiBase = config.public.apiBase;

        if (process.client) {
            this.fetchOrders();
        }
    },

    methods: {
        /* ================= ORDERS ================= */
        async fetchOrders() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) {
                this.loading = false;
                return message.warning("Vui lòng đăng nhập");
            }

            try {
                const res = await fetch(
                    `${this.apiBase}/api/orders/list/${user.id}`
                );
                const data = await res.json();
                this.orders = data.data || [];
            } catch (e) {
                console.error(e);
                message.error("Không tải được đơn hàng");
            } finally {
                this.loading = false;
            }
        },

        async showOrderDetail(id) {
            try {
                const res = await fetch(
                    `${this.apiBase}/api/orders/detail/${id}`
                );
                const data = await res.json();

                this.orderDetail = data.data.order;
                this.orderDetail.items = data.data.items;
                this.modalVisible = true;

            } catch (e) {
                console.error(e);
                message.error("Lỗi tải chi tiết đơn");
            }
        },

        handleCancel() {
            this.modalVisible = false;
            this.orderDetail = null;
        },

        /* ================= CANCEL ================= */
        showModalCancel(id) {
            this.modalVisibleCancel = true;
            this.idCancel = id;
        },

        async cancelOrder() {
            try {
                const res = await fetch(
                    `${this.apiBase}/api/orders/cancel/${this.idCancel}`,
                    { method: "PUT" }
                );
                const data = await res.json();

                if (data.success) {
                    message.success("Đã hủy đơn");
                    this.modalVisibleCancel = false;
                    this.idCancel = null;
                    this.fetchOrders();
                }
            } catch (e) {
                console.error(e);
                message.error("Hủy đơn thất bại");
            }
        },

        handleCancelOrder() {
            this.modalVisibleCancel = false;
            this.idCancel = null;
        },

        /* ================= HELPERS ================= */
        formatDate(dt) {
            return new Date(dt).toLocaleString("vi-VN");
        },

        formatPrice(price) {
            return new Intl.NumberFormat("vi-VN", {
                style: "currency",
                currency: "VND"
            }).format(price);
        },

        getStatusColor(code) {
            return {
                1: "orange",
                2: "blue",
                3: "cyan",
                4: "green",
                5: "red"
            }[code];
        },

        getFormatStatus(code) {
            return {
                1: "Chờ xác nhận",
                2: "Đã xác nhận",
                3: "Đang giao",
                4: "Đã nhận",
                5: "Đã hủy"
            }[code];
        }
    }
};
</script>
