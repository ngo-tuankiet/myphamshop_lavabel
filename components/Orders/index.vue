<template>
    <div>
        <a-table 
            :columns="columns" 
            :data-source="orders" 
            :loading="loading" 
            rowKey="id"
        >
            <!-- STATUS -->
            <template #status="{ text }">
                {{ getFormatStatus(text) }}
            </template>

            <!-- ACTION -->
            <template #action="{ record }">
                <a-button
                    type="primary"
                    style="margin-right: 10px;background-color:#fff;color:#000"
                    size="small"
                    @click="showOrderDetail(record.id)"
                >
                    <EyeOutlined />
                </a-button>

                <a-button
                    v-if="record.status === 1"
                    type="primary"
                    style="color:red;background-color:#fff"
                    size="small"
                    @click="showModalCancel(record.id)"
                >
                    <CloseCircleOutlined />
                </a-button>
            </template>

            <!-- TOTAL_AMOUNT FORMAT -->
            <template #total_amount="{ text }">
                {{ formatPrice(text) }}
            </template>
        </a-table>

        <!-- ========================= -->
        <!--    MODAL CHI TIẾT ĐƠN     -->
        <!-- ========================= -->

        <a-modal 
            :visible="modalVisible" 
            title="Chi tiết đơn hàng"
            width="800px"
            @cancel="handleCancel"
            :footer="null"
        >
            <div v-if="orderDetail">

                <!-- Thông tin đơn -->
                <h3>Thông tin đơn hàng #{{ orderDetail.code }}</h3>

                <a-descriptions :column="2">
                    <a-descriptions-item label="Ngày đặt">
                        {{ formatDate(orderDetail.created_at) }}
                    </a-descriptions-item>

                    <a-descriptions-item label="Trạng thái">
                        <a-tag :color="getStatusColor(orderDetail.status)">
                            {{ getFormatStatus(orderDetail.status) }}
                        </a-tag>
                    </a-descriptions-item>

                    <a-descriptions-item label="Khách hàng">
                        {{ orderDetail.customer_name }}
                    </a-descriptions-item>

                    <a-descriptions-item label="SĐT">
                        {{ orderDetail.customer_phone }}
                    </a-descriptions-item>

                    <a-descriptions-item label="Địa chỉ">
                        {{ orderDetail.customer_address }}
                    </a-descriptions-item>

                    <a-descriptions-item v-if="orderDetail.note" label="Ghi chú">
                        {{ orderDetail.note }}
                    </a-descriptions-item>
                </a-descriptions>

                <!-- Chi tiết SP -->
                <h3 class="mt-4">Chi tiết sản phẩm</h3>

                <a-table 
                    :columns="detailColumns" 
                    :data-source="orderDetail.items"
                    :pagination="false"
                    rowKey="id"
                >
                    <template #images="{ text }">
                        <img 
                            :src="text[0]" 
                            style="width: 50px; height: 50px; object-fit: cover;" 
                        />
                    </template>

                    <template #price="{ text }">
                        {{ formatPrice(text) }}
                    </template>

                    <template #totalPrice="{ record }">
                        {{ formatPrice(record.price * record.quantity) }}
                    </template>
                </a-table>

                <div class="text-right mt-4">
                    <h3>Tổng tiền: {{ formatPrice(orderDetail.total_amount) }}</h3>
                </div>

            </div>
        </a-modal>

        <!-- ========================= -->
        <!--     MODAL HỦY ĐƠN        -->
        <!-- ========================= -->

        <a-modal 
            :visible="modalVisibleCancel"
            title="Xác nhận hủy đơn hàng"
            width="500px"
            @cancel="handleCancelOrder"
            @ok="cancelOrder"
        >
            <p>Bạn chắc chắn muốn hủy đơn hàng này?</p>
        </a-modal>
    </div>
</template>

<script>
import { message } from "ant-design-vue";
import { EyeOutlined, CloseCircleOutlined } from "@ant-design/icons-vue";

export default {
    components: { EyeOutlined, CloseCircleOutlined },

    data() {
        return {
            orders: [],
            loading: true,

            modalVisible: false,
            orderDetail: null,

            modalVisibleCancel: false,
            idCancel: null,

            columns: [
                { title: "Mã đơn", dataIndex: "id", key: "id" },
                { title: "Ngày", dataIndex: "created_at", key: "created_at" },
                { title: "Tổng tiền", dataIndex: "total_amount", key: "total_amount", slots: { customRender: "total_amount" }},
                { title: "Trạng thái", dataIndex: "status", key: "status", slots: { customRender: "status" }},
                { title: "Thao tác", key: "action", width: 120, slots: { customRender: "action" } }
            ],

            detailColumns: [
                { title: "Tên sản phẩm", dataIndex: "product_name", key: "product_name" },
                { title: "SL", dataIndex: "quantity", key: "quantity" },
                { title: "Đơn giá", dataIndex: "price", key: "price", slots: { customRender: "price" }},
                { title: "Thành tiền", key: "totalPrice", slots: { customRender: "totalPrice" }},
            ],
        };
    },

    mounted() {
        this.fetchOrders();
    },

    methods: {
        async fetchOrders() {
            const user = JSON.parse(sessionStorage.getItem("user"));
            if (!user) return message.error("Vui lòng đăng nhập");

            try {
                const res = await fetch(`http://127.0.0.1:8000/api/orders/list/${user.id}`);
                const data = await res.json();
                this.orders = data.data || [];
            } catch {
                message.error("Không tải được đơn hàng");
            } finally {
                this.loading = false;
            }
        },

        async showOrderDetail(id) {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/orders/detail/${id}`);
                const data = await res.json();

                this.orderDetail = data.data.order;
                this.orderDetail.items = data.data.items;
                this.modalVisible = true;

            } catch {
                message.error("Lỗi tải chi tiết đơn");
            }
        },

        handleCancel() {
            this.modalVisible = false;
            this.orderDetail = null;
        },

        showModalCancel(id) {
            this.modalVisibleCancel = true;
            this.idCancel = id;
        },

        async cancelOrder() {
            try {
                const res = await fetch(`http://127.0.0.1:8000/api/orders/cancel/${this.idCancel}`, {
                    method: "PUT"
                });
                const data = await res.json();

                if (data.success) {
                    message.success("Đã hủy đơn");
                    this.modalVisibleCancel = false;
                    this.idCancel = null;
                    this.fetchOrders();
                }
            } catch {
                message.error("Hủy đơn thất bại");
            }
        },

        handleCancelOrder() {
            this.modalVisibleCancel = false;
            this.idCancel = null; // FIX BUG
        },

        // Helpers
        formatDate(dt) {
            return new Date(dt).toLocaleString("vi-VN");
        },

        formatPrice(price) {
            return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(price);
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
        },
    }
};
</script>

<style scoped>
.mt-4 { margin-top: 16px; }
.text-right { text-align: right; }
</style>
