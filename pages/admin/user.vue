<template>
  <div>
    <a-card title="Quản lý tài khoản">
      <template #extra>
        <a-button type="primary" @click="showModal">Thêm tài khoản</a-button>
      </template>

      <!-- BẢNG USER -->
      <a-table :dataSource="users" :columns="columns" rowKey="id">
        <template #bodyCell="{ column, record }">
          <!-- ROLE -->
          <template v-if="column.key === 'role'">
            <a-tag :color="record.role === 2 ? 'red' : 'blue'">
              {{ record.role === 2 ? 'Admin' : 'User' }}
            </a-tag>
          </template>

          <!-- ACTION -->
          <template v-if="column.key === 'action'">
            <a-space>
              <a-button type="primary" @click="handleEdit(record)">Sửa</a-button>
              <a-popconfirm
                title="Xóa tài khoản này?"
                @confirm="handleDelete(record.id)"
                v-if="record.role !== 2"
              >
                <a-button danger>Xóa</a-button>
              </a-popconfirm>
            </a-space>
          </template>
        </template>
      </a-table>

      <!-- MODAL THÊM / SỬA -->
      <a-modal
        :title="modalTitle"
        v-model:visible="visible"
        @ok="handleOk"
        @cancel="handleCancel"
        :confirmLoading="confirmLoading"
        destroyOnClose
      >
        <a-form :model="formState" :rules="rules" ref="formRef" layout="vertical">
          <a-form-item label="Tên đăng nhập" name="username">
            <a-input v-model:value="formState.username" :disabled="!!currentId" />
          </a-form-item>

          <a-form-item label="Email" name="email">
            <a-input v-model:value="formState.email" />
          </a-form-item>

          <a-form-item label="Họ và tên" name="fullname">
            <a-input v-model:value="formState.fullname" />
          </a-form-item>

          <a-form-item label="Vai trò" name="role">
            <a-select v-model:value="formState.role">
              <a-select-option :value="1">User</a-select-option>
              <a-select-option :value="2">Admin</a-select-option>
            </a-select>
          </a-form-item>

          <a-form-item label="Mật khẩu" name="password" :required="!currentId">
            <a-input-password
              v-model:value="formState.password"
              :placeholder="currentId ? 'Để trống nếu không đổi mật khẩu' : 'Nhập mật khẩu'"
            />
          </a-form-item>
        </a-form>
      </a-modal>
    </a-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'

definePageMeta({ layout: 'admin', keepalive: false })

// === RUNTIME CONFIG ===
const config = useRuntimeConfig()
const API_BASE = config.public.API_BASE + '/api/admin/users'

// ===== HELPER =====
const apiFetch = async (path = '', options = {}) => {
  const res = await fetch(`${API_BASE}${path}`, options)
  const json = await res.json()
  if (!res.ok) throw new Error(json.error || 'Lỗi API')
  return json
}

// ======================== STATE =============================
const users = ref([])
const visible = ref(false)
const confirmLoading = ref(false)
const modalTitle = ref('Thêm tài khoản')
const currentId = ref(null)
const formRef = ref(null)

const initialFormState = {
  username: '',
  email: '',
  password: '',
  fullname: '',
  role: 1,
}
const formState = reactive({ ...initialFormState })

// ======================== TABLE COLUMNS ======================
const columns = [
  { title: 'ID', dataIndex: 'id', key: 'id' },
  { title: 'Tên đăng nhập', dataIndex: 'username', key: 'username' },
  { title: 'Email', dataIndex: 'email', key: 'email' },
  { title: 'Họ và tên', dataIndex: 'fullname', key: 'fullname' },
  { title: 'Vai trò', dataIndex: 'role', key: 'role' },
  { title: 'Thao tác', key: 'action' },
]

// ======================== RULES ==============================
const rules = {
  username: [
    { required: true, message: 'Vui lòng nhập tên đăng nhập' },
    { min: 3, message: 'Tên đăng nhập ít nhất 3 ký tự' },
  ],
  email: [
    { required: true, message: 'Vui lòng nhập email' },
    { type: 'email', message: 'Email không hợp lệ' },
  ],
  fullname: [{ required: true, message: 'Vui lòng nhập họ tên' }],
  password: [
    { required: () => !currentId.value, message: 'Nhập mật khẩu' },
    { min: 6, message: 'Mật khẩu ít nhất 6 ký tự' },
  ],
  role: [{ required: true, message: 'Vui lòng chọn vai trò' }],
}

// ======================== FETCH USERS =========================
const fetchUsers = async () => {
  try {
    const json = await apiFetch()
    users.value = json.data.data
  } catch (err) {
    message.error('Không thể tải danh sách user')
  }
}

onMounted(fetchUsers)

// ======================== CRUD FUNCTIONS =======================
const showModal = () => {
  visible.value = true
  modalTitle.value = 'Thêm tài khoản'
  currentId.value = null
  resetForm()
}

const handleEdit = (record) => {
  modalTitle.value = 'Sửa tài khoản'
  currentId.value = record.id
  Object.assign(formState, {
    username: record.username,
    fullname: record.fullname,
    email: record.email,
    role: record.role,
    password: '',
  })
  visible.value = true
}

const handleOk = () => {
  formRef.value.validate().then(async () => {
    confirmLoading.value = true

    const url = currentId.value ? `/${currentId.value}` : ''
    const method = currentId.value ? 'PUT' : 'POST'

    const payload = { ...formState }
    if (currentId.value && !payload.password) delete payload.password

    try {
      await apiFetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      })

      message.success(currentId.value ? 'Cập nhật thành công' : 'Thêm mới thành công')
      visible.value = false
      confirmLoading.value = false
      fetchUsers()
    } catch (err) {
      message.error(err.message)
      confirmLoading.value = false
    }
  })
}

const handleDelete = async (id) => {
  try {
    await apiFetch(`/${id}`, { method: 'DELETE' })
    message.success('Xóa thành công')
    fetchUsers()
  } catch (err) {
    message.error(err.message)
  }
}

const resetForm = () => {
  Object.assign(formState, initialFormState)
  formRef.value?.resetFields()
}

const handleCancel = () => {
  visible.value = false
  resetForm()
}
</script>

<style scoped>
.ant-table-wrapper {
  margin-top: 15px;
}
</style>
