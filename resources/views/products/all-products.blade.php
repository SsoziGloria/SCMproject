@extends(auth()->user()->role . '.app')

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')

  <div class="pagetitle">
    <h1>Product Management</h1>
    <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
      <li class="breadcrumb-item active">Products</li>
    </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">

    {{-- Summary Cards Row --}}
    <div class="row mb-4">
    <div class="col-lg-3 col-md-6">
      <div class="card info-card sales-card">
      <div class="card-body">
        <h5 class="card-title">Total Products</h5>
        <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-box-seam"></i>
        </div>
        <div class="ps-3">
          <h6>{{ $totalProducts }}</h6>
          <span class="text-success small pt-1 fw-bold">{{ $activeProducts }}</span>
          <span class="text-muted small pt-2 ps-1">active</span>
        </div>
        </div>
      </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Low Stock Items</h5>
        <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="ps-3">
          <h6 class="text-warning">{{ $lowStockCount }}</h6>
          <span class="text-muted small pt-2 ps-1">need reorder</span>
        </div>
        </div>
      </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card info-card customers-card">
      <div class="card-body">
        <h5 class="card-title">Categories</h5>
        <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-tags"></i>
        </div>
        <div class="ps-3">
          <h6>{{ $categoriesCount }}</h6>
          <span class="text-muted small pt-2 ps-1">active categories</span>
        </div>
        </div>
      </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card info-card sales-card">
      <div class="card-body">
        <h5 class="card-title">Total Value</h5>
        <div class="d-flex align-items-center">
        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
          <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="ps-3">
          <h6>UGX {{ number_format($totalInventoryValue, 0) }}</h6>
          <span class="text-muted small pt-2 ps-1">Inventory value</span>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>

    {{-- Main Products Table --}}
    <div class="row">
    <div class="col-lg-12">
      <div class="card">
      <div class="card-body">

        {{-- Header with Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title mb-0">All Products</h5>
        <div class="d-flex gap-2">
          @can('create', App\Models\Product::class)
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-circle"></i> Add Product
        </button>
      @endcan
          <button type="button" class="btn btn-outline-success" onclick="exportProducts()">
          <i class="bi bi-download"></i> Export
          </button>
        </div>
        </div>

        {{-- Filters and Search --}}
        <div class="row mb-3">
        <div class="col-md-3">
          <div class="form-group">
          <label for="categoryFilter" class="form-label">Category</label>
          <select class="form-select" id="categoryFilter" onchange="filterProducts()">
            <option value="">All Categories</option>
            @foreach($categories as $category)
        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
        @endforeach
          </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
          <label for="supplierFilter" class="form-label">Supplier</label>
          <select class="form-select" id="supplierFilter" onchange="filterProducts()">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $supplier)
        <option value="{{ $supplier->vendor_id }}">{{ $supplier->name }}</option>
        @endforeach
          </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
          <label for="stockFilter" class="form-label">Stock Status</label>
          <select class="form-select" id="stockFilter" onchange="filterProducts()">
            <option value="">All Stock Levels</option>
            <option value="in-stock">In Stock</option>
            <option value="low-stock">Low Stock</option>
            <option value="out-of-stock">Out of Stock</option>
          </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
          <label for="searchProducts" class="form-label">Search</label>
          <input type="text" class="form-control" id="searchProducts" placeholder="Search products..."
            onkeyup="searchProducts()">
          </div>
        </div>
        </div>

        {{-- Products Table --}}
        <div class="table-responsive">
        <table class="table table-striped" id="productsTable">
          <thead>
          <tr>
            <th scope="col">
            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            </th>
            <th scope="col">Image</th>
            <th scope="col">Product ID</th>
            <th scope="col">Name</th>
            <th scope="col">Category</th>
            <th scope="col">Price</th>
            <th scope="col">Stock</th>
            <th scope="col">Supplier</th>
            <th scope="col">Status</th>
            <th scope="col">Actions</th>
          </tr>
          </thead>
          <tbody>
          @forelse($products as $product)
          <tr data-product-id="{{ $product->id }}">
          <td>
          <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
          </td>
          <td>
          @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded"
          width="50" height="50" style="object-fit: cover;">
        @else
        <div class="bg-light rounded d-flex align-items-center justify-content-center"
          style="width: 50px; height: 50px;">
          <i class="bi bi-image text-muted"></i>
        </div>
        @endif
          </td>
          <td>
          <span class="badge bg-secondary">{{ $product->product_id }}</span>
          </td>
          <td>
          <strong>{{ $product->name }}</strong>
          @if($product->featured)
        <span class="badge bg-warning ms-1">Featured</span>
        @endif
          </td>
          <td>
          @if($product->category)
        <span class="badge bg-info">{{ ucfirst($product->category) }}</span>
        @else
        <span class="text-muted">-</span>
        @endif
          </td>
          <td>
          <strong>UGX {{ number_format($product->price, 0) }}</strong>
          </td>
          <td>
          @php
          $stockClass = 'success';
          if ($product->stock <= 10)
          $stockClass = 'danger';
          elseif ($product->stock <= 50)
          $stockClass = 'warning';
        @endphp
          <span class="badge bg-{{ $stockClass }}">{{ $product->stock }}</span>
          @if($product->inventories_sum_quantity && $product->inventories_sum_quantity != $product->stock)
        <small class="text-muted d-block">({{ $product->inventories_sum_quantity }} in inventory)</small>
        @endif
          </td>
          <td>
          @if($product->supplier)
        <a href="#" class="text-decoration-none">
          {{-- {{ route('suppliers.show', $product->supplier->id) }} --}}
          {{ $product->supplier->name }}
        </a>
        @else
        <span class="text-muted">-</span>
        @endif
          </td>
          <td>
          @if($product->stock > 0)
        <span class="badge bg-success">Active</span>
        @else
        <span class="badge bg-secondary">Out of Stock</span>
        @endif
          </td>
          <td>
          <div class="btn-group" role="group">
            <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm"
            title="View">
            <i class="bi bi-eye"></i>
            </a>
            @can('update', $product)
          <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-warning btn-sm"
          title="Edit">
          <i class="bi bi-pencil"></i>
          </a>
        @endcan
            @can('delete', $product)
          <button type="button" class="btn btn-outline-danger btn-sm"
          onclick="deleteProduct({{ $product->id }})" title="Delete">
          <i class="bi bi-trash"></i>
          </button>
        @endcan
          </div>
          </td>
          </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center py-4">
          <div class="text-muted">
          <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
          <p>No products found</p>
          @can('create', App\Models\Product::class)
        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
        data-bs-target="#addProductModal">
        Add Your First Product
        </button>
        @endcan
          </div>
          </td>
        </tr>
      @endforelse
          </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
      <div class="d-flex justify-content-center mt-4">
      {{ $products->links() }}
      </div>
      @endif

        {{-- Bulk Actions Bar (Hidden by default) --}}
        <div id="bulkActionsBar" class="alert alert-primary d-none mt-3">
        <div class="d-flex justify-content-between align-items-center">
          <span><strong id="selectedCount">0</strong> products selected</span>
          <div class="btn-group">
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('featured')">
            <i class="bi bi-star"></i> Toggle Featured
          </button>
          <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('category')">
            <i class="bi bi-tags"></i> Change Category
          </button>
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">
            <i class="bi bi-trash"></i> Delete Selected
          </button>
          </div>
        </div>
        </div>

      </div>
      </div>
    </div>
    </div>

  </section>


  {{-- Add Product Modal --}}
  <div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Add New Product</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="#" method="POST" enctype="multipart/form-data"> <!--#removed { route('products.store') }} -->
      @csrf
      <div class="modal-body">
        <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
          <label for="product_id" class="form-label">Product ID/SKU</label>
          <input type="text" class="form-control" id="product_id" name="product_id" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
          <label for="name" class="form-label">Product Name</label>
          <input type="text" class="form-control" id="name" name="name" required>
          </div>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
          <label for="category" class="form-label">Category</label>
          <select class="form-select" id="category" name="category">
            <option value="">Select Category</option>
            <option value="dark-chocolate">Dark Chocolate</option>
            <option value="milk-chocolate">Milk Chocolate</option>
            <option value="white-chocolate">White Chocolate</option>
            <option value="cocoa-powder">Cocoa Powder</option>
            <option value="chocolate-bars">Chocolate Bars</option>
            <option value="truffles">Truffles</option>
            <option value="raw-cocoa">Raw Cocoa</option>
          </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
          <label for="price" class="form-label">Price ($)</label>
          <input type="number" step="0.01" class="form-control" id="price" name="price" required>
          </div>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
          <label for="stock" class="form-label">Initial Stock</label>
          <input type="number" class="form-control" id="stock" name="stock" value="0">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
          <label for="supplier_id" class="form-label">Supplier</label>
          <select class="form-select" id="supplier_id" name="supplier_id">
            <option value="">Select Supplier</option>
            @foreach($suppliers as $supplier)
        <option value="{{ $supplier->vendor_id }}">{{ $supplier->name }}</option>
        @endforeach
          </select>
          </div>
        </div>
        </div>

        <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="mb-3">
        <label for="ingredients" class="form-label">Ingredients</label>
        <textarea class="form-control" id="ingredients" name="ingredients" rows="2"
          placeholder="List main ingredients separated by commas"></textarea>
        </div>

        <div class="mb-3">
        <label for="image" class="form-label">Product Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>

        <div class="mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1">
          <label class="form-check-label" for="featured">
          Featured Product
          </label>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Product</button>
      </div>
      </form>
    </div>
    </div>
  </div>

  <script>
    // Product management JavaScript functions
    function filterProducts() {
    const category = document.getElementById('categoryFilter').value;
    const supplier = document.getElementById('supplierFilter').value;
    const stock = document.getElementById('stockFilter').value;

    // Build query parameters
    const params = new URLSearchParams(window.location.search);

    if (category) params.set('category', category);
    else params.delete('category');

    if (supplier) params.set('supplier', supplier);
    else params.delete('supplier');

    if (stock) params.set('stock', stock);
    else params.delete('stock');

    // Reload page with filters
    window.location.search = params.toString();
    }

    function searchProducts() {
    const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
    const table = document.getElementById('productsTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    }
    }

    function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');

    checkboxes.forEach(checkbox => {
      checkbox.checked = selectAll.checked;
    });

    updateBulkActionsBar();
    }

    function updateBulkActionsBar() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
      bulkBar.classList.remove('d-none');
      selectedCount.textContent = checkboxes.length;
    } else {
      bulkBar.classList.add('d-none');
    }
    }

    // Add event listeners to individual checkboxes
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', updateBulkActionsBar);
    });
    });

    function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
      fetch(`/products/${productId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
        location.reload();
        } else {
        alert('Error deleting product: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the product.');
      });
    }
    }

    function exportProducts() {
    const filters = new URLSearchParams(window.location.search);
    window.location.href = '/products/export?' + filters.toString();
    }

    function bulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);

    if (selectedIds.length === 0) {
      alert('Please select at least one product.');
      return;
    }

    if (action === 'delete' && !confirm(`Are you sure you want to delete ${selectedIds.length} products?`)) {
      return;
    }

    // Handle bulk actions based on type
    fetch('/products/bulk-action', {
      method: 'POST',
      headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Content-Type': 'application/json',
      },
      body: JSON.stringify({
      action: action,
      product_ids: selectedIds
      })
    })
      .then(response => response.json())
      .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
      })
      .catch(error => {
      console.error('Error:', error);
      alert('An error occurred during the bulk action.');
      });
    }
  </script>
@endsection