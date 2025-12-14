<!-- pages/admin/product.vue -->
<template>
  <div>
    <a-card title="Quản lý sản phẩm">
      <template #extra>
        <a-button type="primary" @click="openCreate">Thêm sản phẩm</a-button>
      </template>

      <!-- TABLE -->
      <a-table :columns="columns" :data-source="products" rowKey="id">
        <template #bodyCell="{ column, record }">
          
          <!-- IMAGE COLUMN -->
          <template v-if="column.key === 'images'">
            <div style="display: flex; flex-wrap: wrap;">
              <a-image
                v-for="(img, i) in record.images"
                :key="i"
                :src="img"
                :width="60"
                style="margin-right: 4px; margin-bottom: 4px"
              />
            </div>
          </template>

          <!-- ACTION -->
          <template v-if="column.key === 'action'">
            <a-space>
              <a-button type="primary" @click="openEdit(record)">Sửa</a-button>
              <a-popconfirm
                title="Bạn chắc chắn muốn xóa?"
                @confirm="deleteProduct(record.id)"
              >
                <a-button danger>Xóa</a-button>
              </a-popconfirm>
            </a-space>
          </template>

        </template>
      </a-table>

      <!-- MODAL FORM -->
      <a-modal
        v-model:visible="showModal"
        :title="modalTitle"
        :confirm-loading="loadingSubmit"
        @ok="submitForm"
        destroyOnClose
      >

        <a-form ref="formRef" :model="form" :rules="rules" layout="vertical">

          <a-form-item label="Tên sản phẩm" name="name">
            <a-input v-model:value="form.name" />
          </a-form-item>

          <a-form-item label="Mô tả">
            <a-textarea v-model:value="form.description" />
          </a-form-item>

          <a-form-item label="Giá" name="price">
            <a-input-number v-model:value="form.price" :min="0" style="width: 100%" />
          </a-form-item>

          <a-form-item label="Số lượng" name="stock">
            <a-input-number v-model:value="form.stock" :min="0" style="width: 100%" />
          </a-form-item>

          <!-- BRAND -->
          <a-form-item label="Thương hiệu" name="brand_id">
            <a-select v-model:value="form.brand_id" placeholder="Chọn thương hiệu">
              <a-select-option
                v-for="b in brands"
                :key="b.id"
                :value="b.id"
              >
                {{ b.brand_name }}
              </a-select-option>
            </a-select>
          </a-form-item>

          <!-- CATEGORY -->
          <a-form-item label="Danh mục" name="subcategory_id">
            <a-select v-model:value="form.subcategory_id" placeholder="Chọn danh mục">
              <a-select-option
                v-for="c in categories"
                :key="c.id"
                :value="c.id"
              >
                {{ c.name }}
              </a-select-option>
</a-select>
          </a-form-item>

          <a-form-item label="Mã vạch">
            <a-input v-model:value="form.barcode" />
          </a-form-item>

          <a-form-item label="Xuất xứ">
            <a-input v-model:value="form.origin" />
          </a-form-item>

          <a-form-item label="Nước sản xuất">
            <a-input v-model:value="form.manufacture_country" />
          </a-form-item>

          <a-form-item label="Loại da phù hợp">
            <a-input v-model:value="form.skin_type" />
          </a-form-item>

          <a-form-item label="Dung tích">
            <a-input v-model:value="form.volume" />
          </a-form-item>

          <a-form-item label="Mùi hương">
            <a-input v-model:value="form.scent" />
          </a-form-item>

          <!-- UPLOAD IMAGE -->
          <a-form-item label="Hình ảnh">
            <a-upload
              list-type="picture-card"
              :file-list="fileList"
              :before-upload="() => false"
              @change="onChangeUpload"
              multiple
            >
              <div v-if="fileList.length < 10">
                <plus-outlined />
                <div style="margin-top: 8px">Upload</div>
              </div>
            </a-upload>
          </a-form-item>

        </a-form>
      </a-modal>

    </a-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { message } from 'ant-design-vue'
import { PlusOutlined } from '@ant-design/icons-vue'

definePageMeta({ layout: 'admin' })

/* ================= STATE ================= */

const API = "http://127.0.0.1:8000/api"
const products = ref([])
const brands = ref([])
const categories = ref([])

const showModal = ref(false)
const modalTitle = ref("Thêm sản phẩm")
const loadingSubmit = ref(false)

const formRef = ref(null)
const editingId = ref(null)

const form = reactive({
  name: "",
  description: "",
  price: 0,
  stock: 0,
  brand_id: null,
  subcategory_id: null,
  barcode: "",
  origin: "",
  manufacture_country: "",
  skin_type: "",
  volume: "",
  scent: "",
})

const fileList = ref([])

/* ================= TABLE COLUMNS ================= */

const columns = [
  { title: "ID", dataIndex: "id", key: "id" },
  { title: "Tên", dataIndex: "name", key: "name" },
  { title: "Giá", dataIndex: "price", key: "price" },
  { title: "Số lượng", dataIndex: "stock", key: "stock" },
  { title: "Hình ảnh", key: "images" },
  { title: "Thao tác", key: "action" },
]

/* ================= RULES ================= */

const rules = {
  name: [{ required: true, message: "Vui lòng nhập tên sản phẩm" }],
  price: [{ required: true, message: "Vui lòng nhập giá" }],
  stock: [{ required: true, message: "Vui lòng nhập số lượng" }],
  brand_id: [{ required: true, message: "Vui lòng chọn thương hiệu" }],
  subcategory_id: [{ required: true, message: "Vui lòng chọn danh mục" }],
}

/* ================= LOAD DATA ================= */
const loadProducts = async () => {
  const res = await fetch(`${API}/products`)
  const json = await res.json()

  products.value = json.data.data.map((p) => ({
    ...p,
    images: p.images.map((img) => img.url),
  }))
}

const loadBrands = async () => {
  const res = await fetch(`${API}/brands`)
  const json = await res.json()
  brands.value = json.data.data || []
}

const loadCategories = async () => {
  const res = await fetch(`${API}/categories`)
  const json = await res.json()
  categories.value = json.data || []
}

/* ================= MODAL FORM ================= */

const resetForm = () => {
  Object.assign(form, {
    name: "",
    description: "",
    price: 0,
    stock: 0,
    brand_id: null,
    subcategory_id: null,
    barcode: "",
    origin: "",
    manufacture_country: "",
    skin_type: "",
    volume: "",
    scent: "",
  })
  fileList.value = []
}

const openCreate = () => {
  modalTitle.value = "Thêm sản phẩm"
  editingId.value = null
  resetForm()
  showModal.value = true
}

const openEdit = (record) => {
  modalTitle.value = "Sửa sản phẩm"
  editingId.value = record.id

  Object.assign(form, record)

  form.brand_id = record.brand?.id || record.brand_id
  form.subcategory_id = record.category?.id || record.subcategory_id

  fileList.value = (record.images || []).map((url, i) => ({
    uid: "-" + i,
    url,
    name: "image-" + i,
    status: "done",
  }))

  showModal.value = true
}

/* ================= SUBMIT FORM ================= */

const submitForm = async () => {
  await formRef.value?.validate()

  loadingSubmit.value = true

  try {
    const fd = new FormData()
    for (let k in form) fd.append(k, form[k] ?? "")

    let url = `${API}/products`
    if (editingId.value) {
      fd.append("_method", "PUT")
      url = `${API}/products/${editingId.value}`
    }

    const res = await fetch(url, { method: "POST", body: fd })
    const json = await res.json()

    if (!res.ok) throw new Error(json.error)

    const productId = editingId.value || json.data.id

    const hasFile = fileList.value.some((f) => f.originFileObj)
    if (hasFile) {
      const imgFD = new FormData()
      fileList.value.forEach((f) => {
        if (f.originFileObj) imgFD.append("images[]", f.originFileObj)
      })

      await fetch(`${API}/products/${productId}/images`, {
        method: "POST",
        body: imgFD,
      })
    }

    message.success(editingId.value ? "Cập nhật thành công" : "Thêm sản phẩm thành công")
    showModal.value = false
    loadProducts()
  } catch (err) {
    console.error(err)
    message.error(err.message)
  }

  loadingSubmit.value = false
}

/* ================= DELETE PRODUCT ================= */

const deleteProduct = async (id) => {
  const res = await fetch(`${API}/products/${id}`, { method: "DELETE" })
  const json = await res.json()

  if (res.ok) {
    message.success(json.message)
    loadProducts()
  } else {
    message.error(json.error)
  }
}
const onChangeUpload = ({ fileList: fl }) => {
  fileList.value = fl
}

/* ================= INIT ================= */

onMounted(() => {
  loadProducts()
  loadBrands()
  loadCategories()
})
</script>