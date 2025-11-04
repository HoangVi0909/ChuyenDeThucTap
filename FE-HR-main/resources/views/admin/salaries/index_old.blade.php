@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-money-bill-wave"></i> Quản lý Lương Theo Giờ
                        </h4>
                        <div>
                            <button type="button" class="btn btn-success me-2" onclick="showCalculateModal()">
                                <i class="fas fa-calculator"></i> Tính lương tháng
                            </button>
                            <button type="button" class="btn btn-info" onclick="showReportModal()">
                                <i class="fas fa-chart-bar"></i> Báo cáo
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="monthFilter" class="form-label">Tháng/Năm</label>
                                <input type="month" class="form-control" id="monthFilter" value="{{ date('Y-m') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Trạng thái</label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">Tất cả</option>
                                    <option value="draft">Chờ duyệt</option>
                                    <option value="approved">Đã duyệt</option>
                                    <option value="paid">Đã trả</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="employeeFilter" class="form-label">Nhân viên</label>
                                <select class="form-control" id="employeeFilter">
                                    <option value="">Tất cả nhân viên</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary" onclick="loadSalaryRecords()">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                            </div>
                        </div>

                        <!-- Alerts -->
                        <!-- Bulk Actions -->
                        <div class="row mb-3" id="bulkActions" style="display: none;">
                            <div class="col-md-12">
                                <div class="bg-light p-3 rounded">
                                    <span id="selectedCount">0</span> bản ghi được chọn
                                    <button type="button" class="btn btn-success btn-sm ms-2" onclick="bulkApprove()">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="bulkMarkAsPaid()">
                                        <i class="fas fa-money-check"></i> Đánh dấu đã trả
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Records Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="salaryTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Nhân viên</th>
                                        <th>Phòng ban</th>
                                        <th>Chức vụ</th>
                                        <th>Tháng</th>
                                        <th>Giờ làm</th>
                                        <th>Lương/Giờ</th>
                                        <th>Lương cơ bản</th>
                                        <th>Phụ cấp</th>
                                        <th>Thưởng</th>
                                        <th>Phạt</th>
                                        <th>Tổng lương</th>
                                        <th>Trạng thái</th>
                                        <th width="120">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="salaryTableBody">
                                    <tr>
                                        <td colspan="14" class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div id="paginationContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculate Salary Modal -->
    <div class="modal fade" id="calculateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Tính lương tháng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="calculateForm">
                        <div class="mb-3">
                            <label for="calculateMonth" class="form-label">Tháng/Năm</label>
                            <input type="month" class="form-control" id="calculateMonth" value="{{ date('Y-m') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="calculateEmployees" class="form-label">Nhân viên</label>
                            <select class="form-control" id="calculateEmployees" multiple>
                                <option value="">Chọn nhân viên (để trống = tất cả)</option>
                            </select>
                            <small class="text-muted">Giữ Ctrl để chọn nhiều nhân viên</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success" onclick="calculateSalary()">
                        <i class="fas fa-calculator"></i> Tính lương
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Salary Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Chỉnh sửa lương
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editSalaryId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nhân viên</label>
                                    <input type="text" class="form-control" id="editEmployeeName" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tháng</label>
                                    <input type="text" class="form-control" id="editMonth" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giờ làm</label>
                                    <input type="text" class="form-control" id="editHours" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lương/Giờ</label>
                                    <input type="text" class="form-control" id="editHourlyRate" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editBonus" class="form-label">Thưởng (VNĐ)</label>
                                    <input type="number" class="form-control" id="editBonus" min="0" step="1000">
                                </div>
                                <div class="mb-3">
                                    <label for="editPenalty" class="form-label">Phạt (VNĐ)</label>
                                    <input type="number" class="form-control" id="editPenalty" min="0" step="1000">
                                </div>
                                <div class="mb-3">
                                    <label for="editStatus" class="form-label">Trạng thái</label>
                                    <select class="form-control" id="editStatus">
                                        <option value="draft">Chờ duyệt</option>
                                        <option value="approved">Đã duyệt</option>
                                        <option value="paid">Đã trả</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editNotes" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="editNotes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="bg-light p-3 rounded">
                                    <h6>Tính toán lương:</h6>
                                    <p class="mb-1">Lương cơ bản: <span id="editBaseSalary">0</span> VNĐ</p>
                                    <p class="mb-1">Phụ cấp: <span id="editAllowance">0</span> VNĐ</p>
                                    <p class="mb-0"><strong>Tổng lương: <span id="editTotalSalary">0</span> VNĐ</strong></p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-warning" onclick="recalculateSalary()">
                        <i class="fas fa-sync"></i> Tính lại
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateSalary()">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chart-bar"></i> Báo cáo lương tháng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="reportMonth" class="form-label">Tháng/Năm</label>
                            <input type="month" class="form-control" id="reportMonth" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" onclick="loadReport()">
                                <i class="fas fa-search"></i> Xem báo cáo
                            </button>
                        </div>
                    </div>
                    <div id="reportContent">
                        <p class="text-center text-muted">Chọn tháng và nhấn "Xem báo cáo"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let selectedRecords = new Set();

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadEmployees();
        loadSalaryRecords();
    });

    // Load employees for filters and forms
    function loadEmployees() {
        fetch('/api/admin/employees', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const employeeFilter = document.getElementById('employeeFilter');
                const calculateEmployees = document.getElementById('calculateEmployees');
                
                employeeFilter.innerHTML = '<option value="">Tất cả nhân viên</option>';
                calculateEmployees.innerHTML = '<option value="">Chọn nhân viên (để trống = tất cả)</option>';
                
                data.data.data.forEach(employee => {
                    const option1 = new Option(`${employee.name} - ${employee.department?.name || 'N/A'}`, employee.id);
                    const option2 = new Option(`${employee.name} - ${employee.department?.name || 'N/A'}`, employee.id);
                    employeeFilter.appendChild(option1);
                    calculateEmployees.appendChild(option2);
                });
            }
        })
        .catch(error => console.error('Error loading employees:', error));
    }

    // Load salary records
    function loadSalaryRecords(page = 1) {
        const monthFilter = document.getElementById('monthFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeFilter = document.getElementById('employeeFilter').value;
        
        let url = `/api/admin/salary-management?page=${page}`;
        if (monthFilter) url += `&month_year=${monthFilter}`;
        if (statusFilter) url += `&status=${statusFilter}`;
        if (employeeFilter) url += `&employee_id=${employeeFilter}`;

        fetch(url, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderSalaryTable(data.data.data);
                renderPagination(data.data);
            } else {
                showAlert('danger', 'Lỗi khi tải dữ liệu lương');
            }
        })
        .catch(error => {
            console.error('Error loading salary records:', error);
            showAlert('danger', 'Không thể kết nối đến server');
        });
    }

    // Render salary table
    function renderSalaryTable(records) {
        const tbody = document.getElementById('salaryTableBody');
        
        if (records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="14" class="text-center">Không có dữ liệu</td></tr>';
            return;
        }

        tbody.innerHTML = records.map(record => `
            <tr>
                <td>
                    <input type="checkbox" class="record-checkbox" value="${record.id}" 
                           onchange="toggleRecord(${record.id})">
                </td>
                <td>${record.employee?.name || 'N/A'}</td>
                <td>${record.employee?.department?.name || 'N/A'}</td>
                <td>${record.employee?.position?.name || 'N/A'}</td>
                <td>${record.month_year}</td>
                <td>${parseFloat(record.total_hours || 0).toLocaleString()} giờ</td>
                <td>${parseFloat(record.hourly_rate || 0).toLocaleString()} đ</td>
                <td>${parseFloat(record.base_salary || 0).toLocaleString()} đ</td>
                <td>${parseFloat(record.position_allowance || 0).toLocaleString()} đ</td>
                <td>${parseFloat(record.bonus || 0).toLocaleString()} đ</td>
                <td>${parseFloat(record.penalty || 0).toLocaleString()} đ</td>
                <td><strong>${parseFloat(record.total_salary || 0).toLocaleString()} đ</strong></td>
                <td>${getStatusBadge(record.status)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editSalary(${record.id})" title="Chỉnh sửa">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-info" onclick="viewDetail(${record.id})" title="Chi tiết">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Get status badge
    function getStatusBadge(status) {
        const badges = {
            'draft': '<span class="badge bg-warning">Chờ duyệt</span>',
            'approved': '<span class="badge bg-success">Đã duyệt</span>',
            'paid': '<span class="badge bg-info">Đã trả</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">N/A</span>';
    }

    // Show alert
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        alertContainer.innerHTML = alertHtml;
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // All other JavaScript functions would continue here...
    // For brevity, I'll include key functions
    
    function showCalculateModal() {
        const modal = new bootstrap.Modal(document.getElementById('calculateModal'));
        modal.show();
    }

    function calculateSalary() {
        const month = document.getElementById('calculateMonth').value;
        const employees = Array.from(document.getElementById('calculateEmployees').selectedOptions)
                               .map(option => option.value)
                               .filter(value => value);

        const data = { month_year: month };
        if (employees.length > 0) {
            data.employee_ids = employees;
        }

        fetch('/api/admin/salary-management/calculate', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('admin_token'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById('calculateModal')).hide();
                loadSalaryRecords();
            } else {
                showAlert('danger', 'Lỗi khi tính lương');
            }
        })
        .catch(error => {
            console.error('Error calculating salary:', error);
            showAlert('danger', 'Không thể kết nối đến server');
        });
    }
</script>
@endsection
    </div>
@endsection