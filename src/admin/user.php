<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: #1e293b; color: white; overflow-y: auto; }
        .sidebar .brand { padding: 1.5rem; font-size: 1.5rem; font-weight: bold; color: #dc3545; }
        .sidebar nav a { display: block; padding: 0.875rem 1.5rem; color: white; text-decoration: none; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #334155; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .badge-role { padding: 0.35rem 0.65rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .role-admin { background: #fef3c7; color: #92400e; }
        .role-user { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body class="bg-light">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Quản lý Tài khoản</h2>
        
        <div id="alert"></div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="search" class="form-control" placeholder="Tìm username, email, họ tên...">
                    </div>
                    <div class="col-md-4">
                        <select id="role" class="form-select">
                            <option value="">Tất cả vai trò</option>
                            <option value="1">Người dùng</option>
                            <option value="2">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="load()">
                            <i class="fas fa-search"></i> Tìm
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
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Họ tên</th>
                                <th>Vai trò</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View -->
    <div class="modal fade" id="viewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Chi tiết Tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="details"></div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Chỉnh sửa Tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id">
                    <div class="mb-3">
                        <label>Email *</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Họ tên</label>
                        <input type="text" class="form-control" id="fullname">
                    </div>
                    <div class="mb-3">
                        <label>Vai trò *</label>
                        <select class="form-select" id="userRole" required>
                            <option value="1">Người dùng</option>
                            <option value="2">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-primary" onclick="save()">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = 'http://localhost:8000/admin/users';
        let viewModal, editModal;

        function alert(msg, type = 'success') {
            document.getElementById('alert').innerHTML = `
                <div class="alert alert-${type} alert-dismissible">
                    ${msg}<button class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            setTimeout(() => document.getElementById('alert').innerHTML = '', 3000);
        }

        function getRoleBadge(role) {
            return role === 2 
                ? '<span class="badge-role role-admin"><i class="fas fa-crown me-1"></i>Admin</span>'
                : '<span class="badge-role role-user"><i class="fas fa-user me-1"></i>Người dùng</span>';
        }

        async function load() {
            try {
                document.getElementById('loading').classList.remove('d-none');
                document.getElementById('table').classList.add('d-none');

                const search = document.getElementById('search').value;
                const role = document.getElementById('role').value;
                
                let url = `${API}?`;
                if (search) url += `search=${encodeURIComponent(search)}&`;
                if (role) url += `role=${role}`;

                const res = await fetch(url);
                const data = await res.json();
                const users = data.data?.data || data.data || [];

                const tbody = document.getElementById('tbody');
                tbody.innerHTML = users.length ? users.map(u => `
                    <tr>
                        <td>${u.id}</td>
                        <td><strong>${u.username}</strong></td>
                        <td>${u.email}</td>
                        <td>${u.fullname || '<span class="text-muted">-</span>'}</td>
                        <td>${getRoleBadge(u.role)}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="view(${u.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="edit(${u.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="del(${u.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('') : '<tr><td colspan="6" class="text-center">Không có dữ liệu</td></tr>';

                document.getElementById('loading').classList.add('d-none');
                document.getElementById('table').classList.remove('d-none');
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
                document.getElementById('loading').classList.add('d-none');
            }
        }

        async function view(id) {
            try {
                const res = await fetch(`${API}/${id}`);
                const u = (await res.json()).data;

                let orders = '';
                if (u.orders && u.orders.length) {
                    const statusMap = { 1: 'Chờ xử lý', 2: 'Đang xử lý', 3: 'Đang giao', 4: 'Đã giao', 5: 'Đã hủy' };
                    orders = `<h6 class="mt-4">Lịch sử đơn hàng (${u.orders.length}):</h6><table class="table table-sm"><thead><tr><th>Mã đơn</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead><tbody>`;
                    u.orders.slice(0, 5).forEach(o => {
                        orders += `<tr><td><strong>${o.code}</strong></td><td>${o.total_amount?.toLocaleString('vi-VN')} đ</td><td>${statusMap[o.status] || 'N/A'}</td></tr>`;
                    });
                    orders += '</tbody></table>';
                } else {
                    orders = '<p class="text-muted mt-3">Chưa có đơn hàng</p>';
                }

                document.getElementById('details').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> ${u.id}</p>
                            <p><strong>Username:</strong> ${u.username}</p>
                            <p><strong>Email:</strong> ${u.email}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Họ tên:</strong> ${u.fullname || '<span class="text-muted">-</span>'}</p>
                            <p><strong>Vai trò:</strong> ${getRoleBadge(u.role)}</p>
                        </div>
                    </div>
                    ${orders}`;

                viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
                viewModal.show();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        async function edit(id) {
            try {
                const res = await fetch(`${API}/${id}`);
                const u = (await res.json()).data;

                document.getElementById('id').value = u.id;
                document.getElementById('email').value = u.email;
                document.getElementById('fullname').value = u.fullname || '';
                document.getElementById('userRole').value = u.role;

                editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        async function save() {
            try {
                const id = document.getElementById('id').value;
                const email = document.getElementById('email').value.trim();
                const fullname = document.getElementById('fullname').value.trim();
                const role = document.getElementById('userRole').value;

                if (!email) return alert('Vui lòng nhập email', 'warning');

                const res = await fetch(`${API}/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, fullname: fullname || null, role: parseInt(role) })
                });

                if (!res.ok) throw new Error('Cập nhật thất bại');

                editModal.hide();
                alert('Cập nhật tài khoản thành công!');
                load();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        async function del(id) {
            if (!confirm('Bạn có chắc muốn xóa?')) return;
            try {
                const res = await fetch(`${API}/${id}`, { method: 'DELETE' });
                if (!res.ok) throw new Error('Xóa thất bại');
                alert('Xóa tài khoản thành công!');
                load();
            } catch (e) {
                alert('Lỗi: ' + e.message, 'danger');
            }
        }

        document.addEventListener('DOMContentLoaded', load);
        document.getElementById('search').addEventListener('keypress', e => {
            if (e.key === 'Enter') load();
        });
    </script>
</body>
</html>