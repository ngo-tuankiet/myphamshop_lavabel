<template>
  <div>
    <!-- Tabs trạng thái -->
    <a-tabs v-model:activeKey="activeTab" @change="fetchOrders">
      <a-tab-pane key="1" tab="Chờ xác nhận" />
      <a-tab-pane key="2" tab="Đã xác nhận" />
      <a-tab-pane key="3" tab="Đang giao" />
      <a-tab-pane key="4" tab="Hoàn thành" />
      <a-tab-pane key="5" tab="Đã hủy" />
    </a-tabs>

    <!-- Bảng đơn hàng -->
    <a-table
      :columns="columns"
      :data-source="orders"
      :loading="loading"
      rowKey="id"
    >
      <!-- Ngày đặt -->
      <template #created_at="{ text }">
        {{ formatDate(text) }}
      </template>

      <!-- Tổng tiền -->
      <template #total_amount="{ text }">
        {{ formatPrice(text) }}
      </template>

      <!-- Trạng thái -->
      <template #status="{ text }">
        <a-tag :color="getStatusColor(text)">
          {{ getFormatStatus(text) }}
        </a-tag>
      </template>

      <!-- Thao tác -->
      <template #action="{ record }">
        <a-button
          type="primary"
          style="margin-right: 10px; background-color: #fff; color: black"
          size="small"
          @click="showOrderDetail(record.id)"
        >
          <template #icon>
            <EyeOutlined />
          </template>
        </a-button>

        <!-- Nhận đơn (từ chờ xác nhận -> đã xác nhận) -->
        <a-button
          v-if="record.status === 1"
          type="primary"
          style="color: green; background-color: #fff; margin-right: 10px"
          size="small"
          @click="showModalConfirm(record.id)"
        >
          <template #icon>
            <CheckCircleOutlined />
          </template>
        </a-button>

        <!-- Bàn giao vận chuyển (đã xác nhận -> đang giao) -->
        <a-button
          v-if="record.status === 2"
          type="primary"
          style="color: green; background-color: #fff; margin-right: 10px"
          size="small"
          @click="showModalDelivery(record.id)"
        >
          <template #icon>
            <CheckCircleOutlined />
          </template>
        </a-button>

        <!-- Hủy đơn (từ chờ xác nhận) -->
        <a-button
          v-if="record.status === 1"
          type="primary"
          style="color: red; background-color: #fff; margin-right: 10px"
          size="small"
          @click="showModalCancel(record.id)"
        >
          <template #icon>
            <CloseCircleOutlined />
          </template>
        </a-button>

        <!-- Đánh dấu hoàn thành (từ đang giao) -->
        <a-button
          v-if="record.status === 3"
          type="primary"
          style="color: green; background-color: #fff"
          size="small"
          @click="showModalDone(record.id)"
        >
          <template #icon>
            <CheckCircleOutlined />
          </template>
        </a-button>
      </template>
    </a-table>

    <!-- Modal chi tiết đơn hàng -->
    <a-modal
      :visible="modalVisible"
      title="Chi tiết đơn hàng"
      width="800px"
      @cancel="handleCancel"
      :footer="null"
    >
      <div v-if="orderDetail">
        <div class="order-info">
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
            <a-descriptions-item label="Tên khách hàng">
              {{ orderDetail.customer_name }}
            </a-descriptions-item>
            <a-descriptions-item label="Số điện thoại">
              {{ orderDetail.customer_phone }}
            </a-descriptions-item>
            <a-descriptions-item label="Địa chỉ">
              {{ orderDetail.customer_address }}
            </a-descriptions-item>
            <a-descriptions-item v-if="orderDetail.note" label="Ghi chú">
              {{ orderDetail.note }}
            </a-descriptions-item>
          </a-descriptions>
        </div>

        <div class="order-items mt-4">
          <h3>Chi tiết sản phẩm</h3>
          <a-table
            :columns="detailColumns"
            :data-source="orderDetail.items"
            :pagination="false"
            rowKey="id"
          >
            <!-- Hình ảnh sản phẩm -->
            <template #image="{ record }">
              <img
                :src="record.product?.image || record.product?.thumbnail || ''"
                alt="product"
                style="width: 50px; height: 50px; object-fit: cover"
              />
            </template>

            <!-- Tên sản phẩm -->
            <template #product_name="{ record }">
              {{ record.product?.name || 'Sản phẩm' }}
            </template>

            <!-- Đơn giá -->
            <template #price="{ text }">
              {{ formatPrice(text) }}
            </template>

            <!-- Thành tiền -->
            <template #totalPrice="{ record }">
              {{ formatPrice(record.price * record.quantity) }}
            </template>
          </a-table>

          <div class="total-amount mt-4 text-right">
            <h3>Tổng tiền: {{ formatPrice(orderDetail.total_amount) }}</h3>
          </div>
        </div>
      </div>
    </a-modal>

    <!-- Modal Hủy -->
    <a-modal
      :visible="modalVisibleCancel"
      title="Xác nhận hủy đơn hàng"
      @cancel="handleCancel"
      @ok="cancelOrder"
    >
      <div>Bạn có chắc muốn hủy đơn hàng này không?</div>
    </a-modal>

    <!-- Modal Xác nhận -->
    <a-modal
      :visible="modalVisibleConfirm"
      title="Xác nhận nhận đơn hàng"
      @cancel="handleCancel"
      @ok="confirmOrder"
    >
      <div>Bạn có chắc muốn nhận đơn hàng này không?</div>
    </a-modal>

    <!-- Modal Bàn giao vận chuyển -->
    <a-modal
      :visible="modalVisibleDelivery"
      title="Xác nhận bàn giao đơn vị vận chuyển"
      @cancel="handleCancel"
      @ok="deliveryOrder"
    >
      <div>Đơn hàng đã được bàn giao cho đơn vị vận chuyển?</div>
    </a-modal>

    <!-- Modal Hoàn thành -->
    <a-modal
      :visible="modalVisibleDone"
      title="Xác nhận giao hàng thành công"
      @cancel="handleCancel"
      @ok="doneOrder"
    >
      <div>Đơn hàng đã được giao thành công?</div>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import {
  EyeOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
} from '@ant-design/icons-vue'

definePageMeta({
  layout: 'admin',
  keepalive: false,
})

const API_BASE = 'http://127.0.0.1:8000/api/admin'

const activeTab = ref('1')
const orders = ref([])
const loading = ref(true)

// Modal & state
const modalVisible = ref(false)
const orderDetail = ref(null)

const modalVisibleCancel = ref(false)
const idCancel = ref(null)

const modalVisibleConfirm = ref(false)
const idConfirm = ref(null)

const modalVisibleDelivery = ref(false)
const idDelivery = ref(null)

const modalVisibleDone = ref(false)
const idDone = ref(null)

// Cột bảng danh sách đơn hàng
const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
  },
  {
    title: 'Mã đơn',
    dataIndex: 'code',
    key: 'code',
  },
  {
    title: 'Ngày đặt',
    dataIndex: 'created_at',
    key: 'created_at',
    slots: { customRender: 'created_at' },
  },
  {
    title: 'Khách hàng',
    dataIndex: 'customer_name',
    key: 'customer_name',
  },
  {
    title: 'SĐT',
    dataIndex: 'customer_phone',
    key: 'customer_phone',
  },
  {
    title: 'Tổng tiền',
    dataIndex: 'total_amount',
    key: 'total_amount',
    slots: { customRender: 'total_amount' },
  },
  {
    title: 'Trạng thái',
    dataIndex: 'status',
    key: 'status',
    slots: { customRender: 'status' },
  },
  {
    title: 'Thao tác',
    key: 'action',
    fixed: 'right',
    width: 200,
    slots: { customRender: 'action' },
  },
]

// Cột chi tiết sản phẩm trong đơn
const detailColumns = [
  {
    title: 'Hình ảnh',
    key: 'image',
    dataIndex: 'product',
    width: 80,
    slots: { customRender: 'image' },
  },
  {
    title: 'Tên sản phẩm',
    key: 'product_name',
    dataIndex: 'product',
    slots: { customRender: 'product_name' },
  },
  {
    title: 'Số lượng',
    dataIndex: 'quantity',
    key: 'quantity',
    width: 100,
  },
  {
    title: 'Đơn giá',
    dataIndex: 'price',
    key: 'price',
    width: 150,
    slots: { customRender: 'price' },
  },
  {
    title: 'Thành tiền',
    key: 'totalPrice',
    width: 150,
    slots: { customRender: 'totalPrice' },
  },
]

// Lấy danh sách đơn hàng
const fetchOrders = async () => {
  loading.value = true
  try {
    const status = activeTab.value // 1..5
    const res = await fetch(`${API_BASE}/orders?status=${status}`)
    const json = await res.json()

    // json.data là paginator, json.data.data mới là mảng đơn hàng
    if (res.ok) {
      const list = json.data?.data || json.data || []
      orders.value = list
    } else {
      message.error(json.message || 'Lỗi khi tải danh sách đơn hàng')
    }
  } catch (err) {
    console.error(err)
    message.error('Có lỗi xảy ra khi tải danh sách đơn hàng')
  } finally {
    loading.value = false
  }
}

// Chi tiết đơn
const showOrderDetail = async (orderId) => {
  try {
    const res = await fetch(`${API_BASE}/orders/${orderId}`)
    const json = await res.json()

    if (res.ok) {
      orderDetail.value = json.data
      modalVisible.value = true
    } else {
      message.error(json.message || 'Không thể lấy chi tiết đơn hàng')
    }
  } catch (err) {
    console.error(err)
    message.error('Có lỗi xảy ra khi tải chi tiết đơn hàng')
  }
}

// Mở các modal
const showModalCancel = (id) => {
  idCancel.value = id
  modalVisibleCancel.value = true
}

const showModalConfirm = (id) => {
  idConfirm.value = id
  modalVisibleConfirm.value = true
}

const showModalDelivery = (id) => {
  idDelivery.value = id
  modalVisibleDelivery.value = true
}

const showModalDone = (id) => {
  idDone.value = id
  modalVisibleDone.value = true
}

// Hàm gọi API update status
const updateOrderStatus = async (id, status, successMsg, errorMsg) => {
  try {
    const res = await fetch(`${API_BASE}/orders/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({ status }),
    })
    const json = await res.json()

    if (res.ok) {
      message.success(successMsg)
      await fetchOrders()
      handleCancel()
    } else {
      message.error(json.error || errorMsg)
    }
  } catch (err) {
    console.error(err)
    message.error(errorMsg)
  }
}

// Hủy đơn
const cancelOrder = () => {
  if (!idCancel.value) return
  updateOrderStatus(
    idCancel.value,
    5,
    'Hủy đơn hàng thành công',
    'Có lỗi xảy ra khi hủy đơn hàng',
  )
}

// Nhận đơn
const confirmOrder = () => {
  if (!idConfirm.value) return
  updateOrderStatus(
    idConfirm.value,
    2,
    'Xác nhận đơn hàng thành công',
    'Có lỗi xảy ra khi xác nhận đơn hàng',
  )
}

// Bàn giao vận chuyển
const deliveryOrder = () => {
  if (!idDelivery.value) return
  updateOrderStatus(
    idDelivery.value,
    3,
    'Đơn hàng đã bàn giao cho đơn vị vận chuyển',
    'Có lỗi xảy ra khi bàn giao đơn vị vận chuyển',
  )
}

// Đánh dấu đã giao xong
const doneOrder = () => {
  if (!idDone.value) return
  updateOrderStatus(
    idDone.value,
    4,
    'Đơn hàng đã được giao thành công',
    'Có lỗi xảy ra khi cập nhật trạng thái hoàn thành',
  )
}

// Đóng tất cả modal
const handleCancel = () => {
  modalVisible.value = false
  modalVisibleCancel.value = false
  modalVisibleConfirm.value = false
  modalVisibleDelivery.value = false
  modalVisibleDone.value = false
  orderDetail.value = null
}

// Format helpers
const formatDate = (date) => {
  return new Date(date).toLocaleString('vi-VN')
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(price || 0)
}

const getFormatStatus = (status) => {
  switch (status) {
    case 1:
      return 'Chờ xác nhận'
    case 2:
      return 'Đã xác nhận'
    case 3:
      return 'Đang giao hàng'
    case 4:
      return 'Đã nhận hàng'
    case 5:
      return 'Đã hủy đơn'
    default:
      return 'Không xác định'
  }
}

const getStatusColor = (status) => {
  const map = {
    1: 'orange',
    2: 'blue',
    3: 'cyan',
    4: 'green',
    5: 'red',
  }
  return map[status] || 'default'
}

onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
.ant-table-wrapper {
  margin-top: 20px;
}
</style>
