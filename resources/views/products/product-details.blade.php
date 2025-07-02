@extends(auth()->user()->role . '.app')

@section('content')
  {{-- Product Details Page - Chocolate Supply Chain Management --}}



  <div class="pagetitle">
    <h1>Product Details</h1>
    <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
      <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section profile">

    {{-- Product Overview Row --}}
    <div class="row">

    {{-- Product Information Card --}}
    <div class="col-xl-4">
      <div class="card">
      <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

        {{-- Product Image --}}
        @if($product->image)
      <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded-3 mb-3"
      style="width: 200px; height: 200px; object-fit: cover;">
      @else
      <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3"
      style="width: 200px; height: 200px;">
      <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
      </div>
      @endif

        {{-- Product Basic Info --}}
        <h2>{{ $product->name }}</h2>
        <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary">{{ $product->product_id }}</span>
        @if($product->featured)
      <span class="badge bg-warning">Featured</span>
      @endif
        </div>

        @if($product->category)
      <h3 class="text-muted">{{ ucfirst($product->category) }}</h3>
      @endif

        {{-- Price and Stock --}}
        <div class="row text-center mt-3 w-100">
        <div class="col-6">
          <h4 class="text-primary">${{ number_format($product->price, 2) }}</h4>
          <small class="text-muted">Price</small>
        </div>
        <div class="col-6">
          @php
      $stockClass = 'success';
      if ($product->stock <= 10)
        $stockClass = 'danger';
      elseif ($product->stock <= 50)
        $stockClass = 'warning';
      @endphp <h4 class="text-{{ $stockClass }}">{{ $product->stock }}</h4>
          <small class="text-muted">Stock</small>
        </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 d-flex gap-2">
        @can('update', $product)
      <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit Product
      </a>
      @endcan
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
          data-bs-target="#adjustStockModal">
          <i class="bi bi-plus-minus"></i> Adjust Stock
        </button>
        </div>

      </div>
      </div>

      {{-- Supplier Information --}}
      @if($product->supplier)
      <div class="card">
      <div class="card-body">
      <h5 class="card-title">Supplier Information</h5>
      <div class="d-flex align-items-center">
      @if($product->supplier->profile_photo)
      <img src="{{ asset('storage/' . $product->supplier->profile_photo) }}" alt="{{ $product->supplier->name }}"
      class="rounded-circle me-3" width="50" height="50">
      @else
      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
      style="width: 50px; height: 50px;">
      <i class="bi bi-person"></i>
      </div>
      @endif
      <div>
        <h6 class="mb-1">
        <a href="{{ route('suppliers.show', $product->supplier->id) }}" class="text-decoration-none">
        {{ $product->supplier->name }}
        </a>
        </h6>
        <small class="text-muted">{{ $product->supplier->email }}</small>
        @if($product->supplier->phone)
      <br><small class="text-muted">{{ $product->supplier->phone }}</small>
      @endif
      </div>
      </div>

      @if($product->supplier->certification_status)
      <div class="mt-3">
      @php
      $certClass = $product->supplier->certification_status === 'approved' ? 'success' :
      ($product->supplier->certification_status === 'pending' ? 'warning' : 'danger');
      @endphp
      <span class="badge bg-{{ $certClass }}">
      {{ ucfirst($product->supplier->certification_status) }} Certification
      </span>
      </div>
      @endif
      </div>
      </div>
    @endif

    </div>

    {{-- Main Content --}}
    <div class="col-xl-8">

      <div class="card">
      <div class="card-body pt-3">

        {{-- Tabs Navigation --}}
        <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
          type="button" role="tab">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button"
          role="tab">Inventory <span class="badge bg-primary">{{ $inventoryBatches->count()
      }}</span></button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button"
          role="tab">Recent Orders <span class="badge bg-success">{{ $recentOrders->count() }}</span></button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="shipments-tab" data-bs-toggle="tab" data-bs-target="#shipments" type="button"
          role="tab">Shipments <span class="badge bg-info">{{ $recentShipments->count()
      }}</span></button>
        </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content pt-3">

        {{-- Overview Tab --}}
        <div class="tab-pane fade show active" id="overview" role="tabpanel">

          {{-- Statistics Cards --}}
          <div class="row mb-4">
          <div class="col-md-3">
            <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-cart-check text-success" style="font-size: 2rem;"></i>
              <h4 class="mt-2">{{ $totalOrdersCount }}</h4>
              <small class="text-muted">Total Orders</small>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-currency-dollar text-primary" style="font-size: 2rem;"></i>
              <h4 class="mt-2">${{ number_format($totalRevenue, 2) }}</h4>
              <small class="text-muted">Total Revenue</small>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-truck text-info" style="font-size: 2rem;"></i>
              <h4 class="mt-2">{{ $totalShipmentsCount }}</h4>
              <small class="text-muted">Shipments</small>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center">
            <div class="card-body">
              <i class="bi bi-boxes text-warning" style="font-size: 2rem;"></i>
              <h4 class="mt-2">{{ $totalInventoryQuantity }}</h4>
              <small class="text-muted">In Inventory</small>
            </div>
            </div>
          </div>
          </div>

          {{-- Product Details --}}
          <div class="row">
          <div class="col-md-6">
            <h5>Product Information</h5>
            <table class="table table-borderless">
            <tr>
              <td><strong>Product ID:</strong></td>
              <td>{{ $product->product_id }}</td>
            </tr>
            <tr>
              <td><strong>Category:</strong></td>
              <td>{{ $product->category ? ucfirst($product->category) : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Price:</strong></td>
              <td>${{ number_format($product->price, 2) }}</td>
            </tr>
            <tr>
              <td><strong>Stock Level:</strong></td>
              <td>
              <span class="badge bg-{{ $stockClass }}">{{ $product->stock }}</span>
              </td>
            </tr>
            <tr>
              <td><strong>Featured:</strong></td>
              <td>
              @if($product->featured)
          <span class="badge bg-warning">Yes</span>
        @else
          <span class="badge bg-secondary">No</span>
        @endif
              </td>
            </tr>
            <tr>
              <td><strong>Created:</strong></td>
              <td>
              @if($product->created_at)
          {{ $product->created_at->format('M d, Y') }}
        @else
          N/A
        @endif
              </td>
            </tr>
            <tr>
              <td><strong>Last Updated:</strong></td>
              @if($product->updated_at)
          {{ $product->updated_at->format('M d, Y') }}
        @else
          N/A
        @endif
            </tr>
            </table>
          </div>

          <div class="col-md-6">
            @if($product->description)
        <h5>Description</h5>
        <p class="text-muted">{{ $product->description }}</p>
        @endif

            @if($product->ingredients)
          <h5>Ingredients</h5>
          <div class="d-flex flex-wrap gap-1">
          @foreach(explode(',', $product->ingredients) as $ingredient)
        <span class="badge bg-light text-dark">{{ trim($ingredient) }}</span>
        @endforeach
          </div>
        @endif

            {{-- Stock Status Alert --}}
            @if($product->stock <= 10)
        <div class="alert alert-warning mt-3">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Low Stock Alert!</strong> Only {{ $product->stock }} units remaining.
        <a href="#" data-bs-toggle="modal" data-bs-target="#adjustStockModal">Restock now</a>
        </div>
        @elseif($product->stock == 0)
        <div class="alert alert-danger mt-3">
        <i class="bi bi-x-circle me-2"></i>
        <strong>Out of Stock!</strong> This product is currently unavailable.
        </div>
        @endif
          </div>
          </div>

        </div>

        {{-- Inventory Tab --}}
        <div class="tab-pane fade" id="inventory" role="tabpanel">
          <div class="d-flex justify-content-between align-items-center mb-3">
          <h5>Inventory Batches</h5>
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#addInventoryModal">
            <i class="bi bi-plus"></i> Add Batch
          </button>
          </div>

          @if($inventoryBatches->count() > 0)
        <div class="table-responsive">
        <table class="table table-hover">
          <thead>
          <tr>
          <th>Batch Number</th>
          <th>Quantity</th>
          <th>Unit</th>
          <th>Location</th>
          <th>Received Date</th>
          <th>Expiration</th>
          <th>Status</th>
          <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          @foreach($inventoryBatches as $batch)
        <tr>
          <td>
          @if($batch->batch_number)
        <span class="badge bg-secondary">{{ $batch->batch_number }}</span>
        @else
        <span class="text-muted">-</span>
        @endif
          </td>
          <td><strong>{{ $batch->quantity }}</strong></td>
          <td>{{ $batch->unit }}</td>
          <td>{{ $batch->location ?? 'N/A' }}</td>
          <td>{{ $batch->received_date ? $batch->received_date->format('M d, Y') : 'N/A' }}</td>
          <td>
          @if($batch->expiration_date)
        @php
        $isExpiringSoon = $batch->expiration_date->diffInDays(now()) <= 30;
        $isExpired = $batch->
        expiration_date->isPast();
        @endphp
        <span class="badge bg-{{ $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'success') }}">
          {{ $batch->expiration_date->format('M d, Y') }}
        </span>
        @else
        <span class="text-muted">N/A</span>
        @endif
          </td>
          <td>
          @php
        $statusClass = $batch->status === 'available' ? 'success' :
        ($batch->status === 'reserved' ? 'warning' : 'secondary');
        @endphp
          <span class="badge bg-{{ $statusClass }}">{{ ucfirst($batch->status) }}</span>
          </td>
          <td>
          <div class="btn-group btn-group-sm">
          <button type="button" class="btn btn-outline-primary"
          onclick="editInventoryBatch({{ $batch->id }})">
          <i class="bi bi-pencil"></i>
          </button>
          <button type="button" class="btn btn-outline-danger"
          onclick="deleteInventoryBatch({{ $batch->id }})">
          <i class="bi bi-trash"></i>
          </button>
          </div>
          </td>
        </tr>
        @endforeach
          </tbody>
        </table>
        </div>
      @else
        <div class="text-center py-4">
        <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">No inventory batches recorded</p>
        </div>
      @endif
        </div>

        {{-- Orders Tab --}}
        <div class="tab-pane fade" id="orders" role="tabpanel">
          <h5>Recent Orders</h5>
          @if($recentOrders->count() > 0)
        <div class="table-responsive">
        <table class="table table-hover">
          <thead>
          <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Order Date</th>
          <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          @foreach($recentOrders as $order)
        <tr>
          <td>
          <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">
          {{ $order->order_number }}
          </a>
          </td>
          <td>{{ $order->user->name }}</td>
          <td>${{ number_format($order->total_amount, 2) }}</td>
          <td>
          @php
        $orderStatusClass = $order->status === 'delivered' ? 'success' :
        ($order->status === 'shipped' ? 'info' :
        ($order->status === 'processing' ? 'warning' : 'secondary'));
        @endphp
          <span class="badge bg-{{ $orderStatusClass }}">{{ ucfirst($order->status) }}</span>
          </td>
          <td>{{ $order->created_at->format('M d, Y') }}</td>
          <td>
          <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-eye"></i> View
          </a>
          </td>
        </tr>
        @endforeach
          </tbody>
        </table>
        </div>

        <div class="text-center mt-3">
        <a href="{{ route('orders.index', ['product_id' => $product->id]) }}" class="btn btn-outline-primary">
          View All Orders for This Product
        </a>
        </div>
      @else
        <div class="text-center py-4">
        <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">No orders yet for this product</p>
        </div>
      @endif
        </div>

        {{-- Shipments Tab --}}
        <div class="tab-pane fade" id="shipments" role="tabpanel">
          <h5>Recent Shipments</h5>
          @if($recentShipments->count() > 0)
        <div class="table-responsive">
        <table class="table table-hover">
          <thead>
          <tr>
          <th>Shipment #</th>
          <th>Quantity</th>
          <th>Status</th>
          <th>Expected Delivery</th>
          <th>Shipped Date</th>
          <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          @foreach($recentShipments as $shipment)
        <tr>
          <td>
          <a href="{{ route('shipments.show', $shipment->id) }}" class="text-decoration-none">
          {{ $shipment->shipment_number }}
          </a>
          </td>
          <td>{{ $shipment->quantity }}</td>
          <td>
          @php
        $shipmentStatusClass = $shipment->status === 'delivered' ? 'success' :
        ($shipment->status === 'shipped' ? 'info' :
        ($shipment->status === 'pending' ? 'warning' : 'danger'));
        @endphp
          <span class="badge bg-{{ $shipmentStatusClass }}">{{ ucfirst($shipment->status) }}</span>
          </td>
          <td>{{ $shipment->expected_delivery ? $shipment->expected_delivery->format('M d, Y') : 'TBD' }}
          </td>
          <td>{{ $shipment->shipped_at ? $shipment->shipped_at->format('M d, Y') : 'Not shipped' }}</td>
          <td>
          <a href="{{ route('shipments.show', $shipment->id) }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-eye"></i> View
          </a>
          </td>
        </tr>
        @endforeach
          </tbody>
        </table>
        </div>

        <div class="text-center mt-3">
        <a href="{{ route('shipments.index', ['product_id' => $product->id]) }}"
          class="btn btn-outline-primary">
          View All Shipments for This Product
        </a>
        </div>
      @else
        <div class="text-center py-4">
        <i class="bi bi-truck text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">No shipments recorded for this product</p>
        </div>
      @endif
        </div>

        </div>{{-- End Tab Content --}}
      </div>
      </div>

    </div>
    </div>

  </section>


  {{-- Adjust Stock Modal --}}
  <div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Adjust Stock - {{ $product->name }}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      @dd($product)
      <form action="{{ route('products.adjust-stock', $product) }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="mb-3">
        <label for="adjustment_type" class="form-label">Adjustment Type</label>
        <select class="form-select" id="adjustment_type" name="adjustment_type" required>
          <option value="add">Add Stock</option>
          <option value="remove">Remove Stock</option>
          <option value="set">Set Exact Amount</option>
        </select>
        </div>

        <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
        <div class="form-text">Current stock: {{ $product->stock }} units</div>
        </div>

        <div class="mb-3">
        <label for="reason" class="form-label">Reason</label>
        <select class="form-select" id="reason" name="reason">
          <option value="restock">Restock</option>
          <option value="sale">Sale</option>
          <option value="damaged">Damaged/Lost</option>
          <option value="return">Return</option>
          <option value="adjustment">Manual Adjustment</option>
          <option value="other">Other</option>
        </select>
        </div>

        <div class="mb-3">
        <label for="notes" class="form-label">Notes (Optional)</label>
        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Stock</button>
      </div>
      </form>
    </div>
    </div>
  </div>

  {{-- Add Inventory Batch Modal --}}
  <div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Add Inventory Batch - {{ $product->name }}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('inventory.store') }}" method="POST">
      @csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <input type="hidden" name="product_name" value="{{ $product->name }}">

      <div class="modal-body">
        <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
          <label for="batch_number" class="form-label">Batch Number</label>
          <input type="text" class="form-control" id="batch_number" name="batch_number">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" class="form-control" id="inventory_quantity" name="quantity" required min="1">
          </div>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
          <label for="unit" class="form-label">Unit</label>
          <select class="form-select" id="unit" name="unit">
            <option value="pcs">Pieces</option>
            <option value="kg">Kilograms</option>
            <option value="lbs">Pounds</option>
            <option value="boxes">Boxes</option>
            <option value="pallets">Pallets</option>
          </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
          <label for="location" class="form-label">Storage Location</label>
          <input type="text" class="form-control" id="location" name="location"
            placeholder="e.g., Warehouse A, Shelf 12">
          </div>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
          <label for="received_date" class="form-label">Received Date</label>
          <input type="date" class="form-control" id="received_date" name="received_date"
            value="{{ date('Y-m-d') }}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
          <label for="expiration_date" class="form-label">Expiration Date</label>
          <input type="date" class="form-control" id="expiration_date" name="expiration_date">
          </div>
        </div>
        </div>

        <div class="mb-3">
        <label for="supplier_id" class="form-label">Supplier</label>
        <select class="form-select" id="inventory_supplier_id" name="supplier_id">
          <option value="">Select Supplier</option>
          @foreach($suppliers as $supplier)
        <option value="{{ $supplier->id }}" {{ $product->supplier_id == $supplier->id ? 'selected' : '' }}>
        {{ $supplier->name }}
        </option>
      @endforeach
        </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Batch</button>
      </div>
      </form>
    </div>
    </div>
  </div>

  <script>
    function editInventoryBatch(batchId) {
    // You can implement an edit modal or redirect to edit page
    window.location.href = `/inventory/${batchId}/edit`;
    }

    function deleteInventoryBatch(batchId) {
    if (confirm('Are you sure you want to delete this inventory batch?')) {
      fetch(`/inventory/${batchId}`, {
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
        alert('Error deleting batch: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the batch.');
      });
    }
    }

    // Auto-calculate new stock level in adjustment modal
    document.getElementById('adjustment_type').addEventListener('change', function () {
    const currentStock = {{ $product->stock
    }};
    const quantityInput = document.getElementById('quantity');
    const type = this.value;

    quantityInput.addEventListener('input', function () {
      let newStock;
      const qty = parseInt(this.value) || 0;

      switch (type) {
      case 'add':
        newStock = currentStock + qty;
        break;
      case 'remove':
        newStock = Math.max(0, currentStock - qty);
        break;
      case 'set':
        newStock = qty;
        break;
      }

      // Update help text to show new stock level
      const helpText = this.nextElementSibling;
      helpText.innerHTML = `Current stock: ${currentStock} units → New stock: ${newStock} units`;
    });
    });
  </script>
@endsection