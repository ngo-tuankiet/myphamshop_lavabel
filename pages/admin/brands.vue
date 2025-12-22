<template>
  <div>
    <a-card title="Quản lý Thương hiệu">
      <!-- BUTTON THÊM -->
      <template #extra>
        <a-button type="primary" @click="openAddModal">
          Thêm thương hiệu
        </a-button>
      </template>

      <!-- SEARCH -->
      <div class="mb-4 flex gap-3">
        <a-input
          v-model:value="search"
          placeholder="Tìm kiếm thương hiệu..."
          style="width: 300px"
          @pressEnter="fetchBrands" />
        <a-button @click="fetchBrands">Tìm</a-button>
      </div>

      <!-- TABLE -->
      <a-table :columns="columns" :data-source="brands" rowKey="id">
        <template #bodyCell="{ column, record }">
          <!-- ACTION -->
          <template v-if="column.key === 'action'">
            <a-space>
              <a-button type="primary" @click="openEditModal(record)"
                >Sửa</a-button
              >

              <a-popconfirm
                title="Xóa thương hiệu?"
                @confirm="deleteBrand(record.id)">
                <a-button danger>Xóa</a-button>
              </a-popconfirm>
            </a-space>
          </template>
        </template>
      </a-table>

      <!-- MODAL -->
      <a-modal
        v-model:visible="modalVisible"
        :title="modalTitle"
        @ok="handleSave"
        :confirmLoading="loading">
        <a-form layout="vertical">
          <a-form-item label="Tên thương hiệu" required>
            <a-input v-model:value="form.brand_name" />
          </a-form-item>
        </a-form>
      </a-modal>
    </a-card>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { message } from "ant-design-vue";

definePageMeta({ layout: "admin" });

const {
  public: { apiBase },
} = useRuntimeConfig();

// API
const API = `${apiBase}/api/brands`;

// STATE
const brands = ref([]);
const search = ref("");
const modalVisible = ref(false);
const modalTitle = ref("Thêm thương hiệu");
const loading = ref(false);
const editId = ref(null);

const form = ref({
  brand_name: "",
});

// TABLE COLUMNS
const columns = [
  { title: "Tên thương hiệu", dataIndex: "brand_name" },
  { title: "Thao tác", key: "action" },
];

// FETCH LIST
async function fetchBrands() {
  try {
    const query = search.value ? `?search=${search.value}` : "";
    const res = await $fetch(API + query);

    brands.value = res.data.data;
  } catch (err) {
    message.error("Lỗi tải danh sách thương hiệu");
  }
}

// OPEN CREATE
function openAddModal() {
  modalVisible.value = true;
  modalTitle.value = "Thêm thương hiệu";
  editId.value = null;
}

// OPEN EDIT
function openEditModal(record) {
  modalVisible.value = true;
  modalTitle.value = "Sửa thương hiệu";
  editId.value = record.id;

  form.value.brand_name = record.brand_name;
}

// SAVE (create + update)
async function handleSave() {
  loading.value = true;

  const fd = new FormData();
  fd.append("brand_name", form.value.brand_name);

  let url = API;

  if (editId.value) {
    url += `/${editId.value}`;
    fd.append("_method", "PUT");
  }

  try {
    await fetch(url, { method: "POST", body: fd });

    message.success(editId.value ? "Cập nhật thành công" : "Thêm thành công");
    modalVisible.value = false;
    fetchBrands();
  } catch (err) {
    message.error("Lỗi khi lưu");
  }

  loading.value = false;
}

// DELETE
async function deleteBrand(id) {
  try {
    const res = await fetch(`${API}/${id}`, { method: "DELETE" });
    const data = await res.json();

    if (data.error) return message.error(data.error);

    message.success("Đã xóa!");
    fetchBrands();
  } catch (err) {
    message.error("Không thể xóa thương hiệu");
  }
}

fetchBrands();
</script>

<style scoped>
.mb-4 {
  margin-bottom: 16px;
}
</style>
