<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Loại sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: #1e293b; color: white; overflow-y: auto; }
        .sidebar .brand { padding: 1.5rem; font-size: 1.5rem; font-weight: bold; color: #dc3545; }
        .sidebar nav a { display: block; padding: 0.875rem 1.5rem; color: white; text-decoration: none; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #334155; }
        .main-content { margin-left: 250px; padding: 2rem; }
    </style>
</head>
<body class="bg-light">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Quản lý Loại sản phẩm</h2>
        
        <div id="alert"></div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <input type="text" id="search" class="form-control" placeholder="Tìm loại sản phẩm...">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success w-100" onclick="showModal()">
                            <i class="fas fa-plus"></i> Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="loading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
                <div id="table" class="d-none">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Tên loại</th>
                                <th>Mô tả</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle">Thêm Loại sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id">
                    <div class="mb-3">
                        <label>Tên loại sản phẩm *</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả</label>
                        <textarea class="form-control" id="desc" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-primary" onclick="save()">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = 'http://localhost:8000/admin/categories';
        let modal;

        function alert(msg, type = 'success') {
            document.getElementById('alert').innerHTML = `
                <div class="alert alert-${type} alert-dismissible">
                    ${msg}<button class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            setTimeout(() => document.getElementById('alert').innerHTML = '', 3000);
        }

        async function load() {
            try {
                document.getElementById('loading').classList.remove('d-none');
                document.getElementById('table').classList.add('d-none');

                const res = await fetch(API);
                const data = await res.json();
                const cats = data.data || [];

                const search = document.getElementById('search').value.toLowerCase();
                const filtered = search ? cats.filter(c => 
                    c.name.toLowerCase().includes(search) || 
                    (c.description || '').toLowerCase().includes(search)
                ) : cats;

                const tbody = document.getElementById('tbody');
                tbody.innerHTML = filtered.length ? filtered.map(c => `
                    <tr>
                        <td>${c.id}</td>
                        <td><strong>${c.name}</strong></td>
                        <td>${c.description || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="edit(${c.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="del(${c.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('') : '<tr><td colspan="4" class="text-center">Không có dữ liệu</td></tr>';

                document.getElementById('loading').classList.add('d-none');
                document.getElementById('table').classList.remove('d-none');
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
                document.getElementById('loading').classList.add('d-none');
            }
        }

        function showModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Loại sản phẩm';
            document.getElementById('id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('desc').value = '';
            modal = new bootstrap.Modal(document.getElementById('modal'));
            modal.show();
        }

        async function edit(id) {
            try {
                const res = await fetch(`${API}/${id}`);
                const data = await res.json();
                const c = data.data;

                document.getElementById('modalTitle').textContent = 'Sửa Loại sản phẩm';
                document.getElementById('id').value = c.id;
                document.getElementById('name').value = c.name;
                document.getElementById('desc').value = c.description || '';
                modal = new bootstrap.Modal(document.getElementById('modal'));
                modal.show();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        async function save() {
            try {
                const id = document.getElementById('id').value;
                const name = document.getElementById('name').value.trim();
                const desc = document.getElementById('desc').value.trim();

                if (!name) return alert('Vui lòng nhập tên', 'warning');

                const url = id ? `${API}/${id}` : API;
                const method = id ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, description: desc || null })
                });

                if (!res.ok) throw new Error('Không thể lưu');

                modal.hide();
                alert(id ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
                load();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        async function del(id) {
            if (!confirm('Bạn có chắc muốn xóa?')) return;
            try {
                const res = await fetch(`${API}/${id}`, { method: 'DELETE' });
                if (!res.ok) throw new Error('Không thể xóa');
                alert('Xóa thành công!');
                load();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        document.addEventListener('DOMContentLoaded', load);
        document.getElementById('search').addEventListener('input', load);
    </script>
</body>
</html>