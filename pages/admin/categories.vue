<!-- pages/admin/category.vue -->
<template>
    <div>
        <a-card title="Quản lý loại sản phẩm">
            <template #extra>
                <a-button type="primary" @click="showModal">
                    Thêm loại sản phẩm
                </a-button>
            </template>

            <!-- TABLE -->
            <a-table :dataSource="categories" :columns="columns" rowKey="id">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'action'">
                        <a-space>
                            <a-button type="primary" @click="handleEdit(record)">
                                Sửa
                            </a-button>

                            <a-popconfirm
                                title="Bạn có chắc muốn xóa?"
                                ok-text="Xóa"
                                cancel-text="Hủy"
                                @confirm="handleDelete(record.id)"
                            >
                                <a-button danger>Xóa</a-button>
                            </a-popconfirm>
                        </a-space>
                    </template>
                </template>
            </a-table>

            <!-- MODAL -->
            <a-modal
                :title="modalTitle"
                v-model:visible="visible"
                :confirmLoading="confirmLoading"
                @ok="handleOk"
                @cancel="handleCancel"
                destroyOnClose
            >
                <a-form :model="formState" :rules="rules" ref="formRef" layout="vertical">
                    <a-form-item label="Tên loại sản phẩm" name="name">
                        <a-input v-model:value="formState.name" />
                    </a-form-item>

                    <a-form-item label="Mô tả" name="description">
                        <a-textarea v-model:value="formState.description" :rows="4" />
                    </a-form-item>
                </a-form>
            </a-modal>
        </a-card>
    </div>
</template>



<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from "vue"
import { message } from "ant-design-vue"

definePageMeta({
    layout: "admin",
    keepalive: false,
})

const { public: { apiBase } } = useRuntimeConfig();

// ==================== STATE ====================
const categories = ref([])
const visible = ref(false)
const confirmLoading = ref(false)
const modalTitle = ref("Thêm loại sản phẩm")
const currentId = ref(null)
const formRef = ref(null)

const controller = new AbortController()
const signal = controller.signal

// Form mặc định
const formState = reactive({
    name: "",
    description: "",
})

// ==================== CỘT BẢNG ====================
const columns = [
    { title: "ID", dataIndex: "id", key: "id" },
    { title: "Tên loại sản phẩm", dataIndex: "name", key: "name" },
    { title: "Mô tả", dataIndex: "description", key: "description" },
    { title: "Thao tác", key: "action" },
]

// ==================== VALIDATION ====================
const rules = {
    name: [{ required: true, message: "Vui lòng nhập tên loại sản phẩm" }],
    description: [{ required: true, message: "Vui lòng nhập mô tả" }],
}

// ==================== FUNCTIONS ====================

// Reset form
const resetForm = () => {
    formState.name = ""
    formState.description = ""
    formRef.value?.resetFields()
}

// Hiển modal thêm
const showModal = () => {
    modalTitle.value = "Thêm loại sản phẩm"
    currentId.value = null
    resetForm()
    visible.value = true
}

// Submit form
const handleOk = () => {
    formRef.value.validate().then(async () => {
        confirmLoading.value = true

        const url = currentId.value
            ? `${apiBase}/api/categories/${currentId.value}`
            : `${apiBase}/api/categories`

        const method = currentId.value ? "PUT" : "POST"

        const res = await fetch(url, {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(formState),
        })

        const json = await res.json()

        if (res.ok) {
            message.success(json.message)
            visible.value = false
            fetchCategories()
        } else {
            message.error(json.error || "Có lỗi xảy ra")
        }

        confirmLoading.value = false
    })
}

// Cancel modal
const handleCancel = () => {
    visible.value = false
    resetForm()
}

// Edit
const handleEdit = (record) => {
    currentId.value = record.id
    modalTitle.value = "Sửa loại sản phẩm"

    formState.name = record.name
    formState.description = record.description

    visible.value = true
}

// Delete
const handleDelete = async (id) => {
    const res = await fetch(`${apiBase}/api/categories/${id}`, {
        method: "DELETE",
    })

    const json = await res.json()

    if (res.ok) message.success(json.message)
    else message.error(json.error)

    fetchCategories()
}

// Get list categories
const fetchCategories = async () => {
    const res = await fetch(`${apiBase}/api/categories`, { signal })
    const json = await res.json()
    categories.value = json.data
}

// ==================== LIFECYCLE ====================
onMounted(() => {
    fetchCategories()
})

onBeforeUnmount(() => {
    controller.abort()
})
</script>



<style scoped></style>
