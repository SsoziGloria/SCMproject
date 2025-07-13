@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">User Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New User
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                        <i class="bi bi-people"></i> Bulk Actions
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.users.export') }}">
                        <i class="bi bi-download"></i> Export Users
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['supplier'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-shop fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Retailers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['retailer'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart4 fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['customer'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search name or email" 
                            name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select class="form-select" name="role" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="supplier" {{ request('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                        <option value="retailer" {{ request('role') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select class="form-select" name="sort" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="bulk-form" action="{{ route('admin.users.bulk-action') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" 
                                                name="user_ids[]" value="{{ $user->id }}" 
                                                {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($user->profile_photo)
                                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" class="rounded-circle" 
                                                        alt="{{ $user->name }}" width="40" height="40" style="object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" 
                                                        style="width: 40px; height: 40px;">
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-decoration-none">
                                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                                </a>
                                                @if($user->id === auth()->id())
                                                    <span class="badge bg-secondary">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ 
                                            $user->role == 'admin' ? 'danger' : 
                                            ($user->role == 'supplier' ? 'warning' : 
                                            ($user->role == 'retailer' ? 'success' : 'primary')) }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-switch" type="checkbox" 
                                                data-user-id="{{ $user->id }}"
                                                {{ $user->is_active ? 'checked' : '' }}
                                                {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(auth()->id() != $user->id)
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal" 
                                                    data-user-id="{{ $user->id }}" 
                                                    data-user-name="{{ $user->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="mt-3">No users found</h5>
                                            <p class="text-muted">Try adjusting your search or filter criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span class="text-muted">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</span>
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="userName"></span>?</p>
                <p class="text-danger">This action cannot be undone. All associated data may be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Select an action to perform on the selected users:</p>
                <div class="mb-3">
                    <select class="form-select" id="bulk-action">
                        <option value="">Select an action...</option>
                        <option value="activate">Activate Users</option>
                        <option value="deactivate">Deactivate Users</option>
                        <option value="delete">Delete Users</option>
                    </select>
                </div>
                <div id="bulk-warning" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle-fill"></i> 
                    This action will affect all selected users except your own account.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="apply-bulk-action" disabled>Apply</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        const userCheckboxes = document.querySelectorAll('.user-checkbox:not([disabled])');
        
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Delete user modal
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                
                document.getElementById('userName').textContent = userName;
                document.getElementById('deleteForm').action = `{{ url('admin/users') }}/${userId}`;
            });
        }
        
        // Status toggle switches
        document.querySelectorAll('.status-switch').forEach(function(switchElem) {
            switchElem.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const isChecked = this.checked;
                
                fetch(`{{ url('admin/users') }}/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        is_active: isChecked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show toast notification
                        const toast = new bootstrap.Toast(document.querySelector('.toast'));
                        document.querySelector('.toast-body').textContent = data.message;
                        toast.show();
                    } else {
                        this.checked = !isChecked; // Revert switch state
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isChecked; // Revert switch state
                });
            });
        });
        
        // Bulk actions
        const bulkActionSelect = document.getElementById('bulk-action');
        const applyBulkActionBtn = document.getElementById('apply-bulk-action');
        const bulkWarning = document.getElementById('bulk-warning');
        
        bulkActionSelect.addEventListener('change', function() {
            applyBulkActionBtn.disabled = !this.value;
            
            if (this.value === 'delete') {
                bulkWarning.classList.remove('d-none');
                bulkWarning.textContent = 'Warning! This will permanently delete all selected users. This action cannot be undone.';
            } else if (this.value === 'deactivate') {
                bulkWarning.classList.remove('d-none');
                bulkWarning.textContent = 'This will deactivate all selected user accounts. They will not be able to login.';
            } else if (this.value === 'activate') {
                bulkWarning.classList.remove('d-none');
                bulkWarning.textContent = 'This will activate all selected user accounts. They will be able to login.';
            } else {
                bulkWarning.classList.add('d-none');
            }
        });
        
        applyBulkActionBtn.addEventListener('click', function() {
            const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
            
            if (selectedUsers.length === 0) {
                alert('Please select at least one user');
                return;
            }
            
            document.getElementById('bulk-form').action = '{{ route("admin.users.bulk-action") }}';
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = bulkActionSelect.value;
            document.getElementById('bulk-form').appendChild(actionInput);
            document.getElementById('bulk-form').submit();
        });
    });
</script>
@endpush