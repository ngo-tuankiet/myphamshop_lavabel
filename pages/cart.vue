<!-- pages/cart.vue -->
<<template>
    <div class="container mx-auto p-4">
        <ClientOnly>

      <a-card title="Giỏ hàng của bạn">

            <a-table v-if="cartItems.length" :dataSource="cartItems" :columns="columns" :pagination="false">
                <template #bodyCell="{ column, record }">

                    <!-- ảnh -->
                    <template v-if="column.key === 'image'">
                        <a-image :src="record.image" :width="80" />
                    </template>

                    <!-- số lượng -->
                    <template v-if="column.key === 'quantity'">
                        <a-input-number 
                            v-model:value="record.quantity" 
                            :min="1"
                            @change="value => updateQuantity(record, value)" />
                    </template>

                    <!-- thành tiền -->
                    <template v-if="column.key === 'total'">
                        {{ formatPrice(record.price * record.quantity) }}
                    </template>

                    <!-- nút xóa -->
                    <template v-if="column.key === 'action'">
                        <a-button danger @click="removeFromCart(record)">Xóa</a-button>
                    </template>

                </template>
            </a-table>

            <!-- nếu trống -->
            <div v-else class="text-center py-8">
                <h3>Giỏ hàng trống</h3>
                <NuxtLink to="/"><a-button type="primary">Tiếp tục mua sắm</a-button></NuxtLink>
            </div>

            <!-- Tổng tiền -->
            <div v-if="cartItems.length" class="text-right mt-4">
                <h2>Tổng tiền: {{ formatPrice(totalAmount) }}</h2>
            </div>

            <!-- Form đặt hàng -->
            <div v-if="cartItems.length" class="mt-8">
                <h2 class="mb-4">Thông tin giao hàng</h2>

                <a-form :model="formState" :rules="rules" layout="vertical" @finish="handleSubmit">

                    <a-form-item label="Họ và tên" name="fullname">
                        <a-input v-model:value="formState.fullname" />
                    </a-form-item>

                    <a-form-item label="Số điện thoại" name="phone">
                        <a-input v-model:value="formState.phone" />
                    </a-form-item>

                    <a-form-item label="Địa chỉ" name="address">
                        <a-textarea v-model:value="formState.address" :rows="4" />
                    </a-form-item>

                    <a-form-item label="Ghi chú" name="note">
                        <a-textarea v-model:value="formState.note" :rows="4" />
                    </a-form-item>

                    <a-form-item>
                        <a-button type="primary" html-type="submit" :loading="submitting">Đặt hàng</a-button>
                    </a-form-item>

                </a-form>
            </div>

        </a-card>
          </ClientOnly>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { message } from 'ant-design-vue'
const router = useRouter()

// giỏ hàng
const cartItems = ref([])
const submitting = ref(false)
const totalAmount = computed(() => {
    return cartItems.value.reduce((sum, item) => {
        return sum + Number(item.price) * Number(item.quantity);
    }, 0);
});
 

// form đặt hàng
const formState = ref({
    fullname: '',
    phone: '',
    address: '',
    note: ''
})

// validate form
const rules = {
    fullname: [{ required: true, message: 'Vui lòng nhập họ tên' }],
    phone: [
        { required: true, message: 'Vui lòng nhập số điện thoại' },
        { pattern: /^[0-9]{10}$/, message: 'Số điện thoại không hợp lệ' }
    ],
    address: [{ required: true, message: 'Vui lòng nhập địa chỉ' }]
}

// columns table
const columns = [
    { title: 'Hình ảnh', key: 'image', width: 100 },
    { title: 'Tên sản phẩm', dataIndex: 'name', key: 'name' },
    { title: 'Đơn giá', dataIndex: 'price', key: 'price' },
    { title: 'Số lượng', key: 'quantity', width: 120 },
    { title: 'Thành tiền', key: 'total' },
    { title: 'Thao tác', key: 'action', width: 100 }
]

// format tiền
const formatPrice = (price) =>
    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price)

// load cart từ API
const loadCart = async () => {
    if (!import.meta.client) return;

    const userRaw = sessionStorage.getItem("user");
    if (!userRaw) {
        cartItems.value = [];
        return;
    }

    const user = JSON.parse(userRaw);

    const res = await $fetch("http://127.0.0.1:8000/api/cart/get", {
        method: "POST",
        body: { user_id: user.id }
    }); 


    cartItems.value = res.cart.map(item => ({
        ...item,
        price: Number(item.price),
        quantity: Number(item.quantity)
    }));
};

const updateQuantity = async (item, value) => {
    await $fetch("http://127.0.0.1:8000/api/cart/update", {
        method: "POST",
        body: {
            cart_id: item.id,
            quantity: value
        }
    })

    message.success("Đã cập nhật")
    loadCart()
}

// xóa item khỏi giỏ
const removeFromCart = async (item) => {
    await $fetch("http://127.0.0.1:8000/api/cart/remove", {
        method: "POST",
        body: { cart_id: item.id }
    })

    message.success("Đã xóa")
    loadCart()  
}

// gửi đặt hàng

    
const handleSubmit = async (values) => {
    submitting.value = true;

    const user = JSON.parse(sessionStorage.getItem("user") || "null");

    const orderBody = {
        fullname: values.fullname,
        phone: values.phone,
        address: values.address,
        note: values.note,
        user_id: user.id,   
        cart: cartItems.value
    };

    try {
        const res = await $fetch("http://127.0.0.1:8000/api/orders/check", {
            method: "POST",
            body: orderBody,
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
        });

        if (res.success) {
            message.success("Đang chuyển đến cổng thanh toán...");
            window.location.href = res.checkoutUrl; // ✔ REDIRECT VNPAY
        } else {
            message.error(res.message || "Lỗi đặt hàng");
        }
    } catch (e) {
        message.error("Đặt hàng thất bại");
    }

    submitting.value = false;
};


onMounted(() => {
if  (process.client) loadCart();
  const route = useRoute();
    if (route.query.success == 1) {
        message.success("Thanh toán thành công!");
    } else if (route.query.success == 0) {
        message.error("Thanh toán thất bại");
    }
});

</script>
