@extends('admin.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Create New Order</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf
            <div class="row">
                <!-- Left Column: Order Details -->
                <div class="col-md-8">
                    <!-- Customer Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer_name" class="form-label">Customer Name*</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                                        value="{{ old('customer_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="sales_channel_id" class="form-label">Sales Channel*</label>
                                    <select class="form-select" id="sales_channel_id" name="sales_channel_id" required>
                                        <option value="">Select Sales Channel</option>
                                        @foreach($salesChannels as $channel)
                                            <option value="{{ $channel->id }}" {{ old('sales_channel_id') == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="shipping_city" name="shipping_city"
                                        value="{{ old('shipping_city') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_region" class="form-label">Region</label>
                                    <select class="form-select" id="shipping_region" name="shipping_region">
                                        <option value="">Select Region</option>
                                        <option value="East" {{ old('shipping_region') == 'East' ? 'selected' : '' }}>East
                                        </option>
                                        <option value="West" {{ old('shipping_region') == 'West' ? 'selected' : '' }}>West
                                        </option>
                                        <option value="North" {{ old('shipping_region') == 'North' ? 'selected' : '' }}>North
                                        </option>
                                        <option value="South" {{ old('shipping_region') == 'South' ? 'selected' : '' }}>South
                                        </option>
                                        <option value="Central" {{ old('shipping_region') == 'Central' ? 'selected' : '' }}>
                                            Central</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Order Items</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addItem">
                                    <i class="bi bi-plus"></i> Add Product
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th width="120">Quantity</th>
                                            <th width="150">Price</th>
                                            <th width="150">Total</th>
                                            <th width="50">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="noItemsRow">
                                            <td colspan="5" class="text-center py-3">No products added yet</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td colspan="2"><strong id="subtotalDisplay">UGX 0</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Additional Notes</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4 sticky-top" style="top: 20px; z-index: 1;">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="summarySubtotal">UGX 0</span>
                            </div>

                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Discount:</label>
                                <div class="input-group">
                                    <span class="input-group-text">UGX</span>
                                    <input type="number" class="form-control" id="discount_amount" name="discount_amount"
                                        value="{{ old('discount_amount', 0) }}" min="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="shipping_fee" class="form-label">Shipping Fee:</label>
                                <div class="input-group">
                                    <span class="input-group-text">UGX</span>
                                    <input type="number" class="form-control" id="shipping_fee" name="shipping_fee"
                                        value="{{ old('shipping_fee', 0) }}" min="0">
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total:</strong>
                                <strong id="orderTotal">UGX 0</strong>
                            </div>

                            <!-- Payment Information -->
                            <div class="mb-3">
                                <label class="form-label">Payment Method*</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment" id="cash" value="cash"
                                        checked>
                                    <label class="form-check-label" for="cash">Cash</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment" id="mobile_money"
                                        value="mobile_money">
                                    <label class="form-check-label" for="mobile_money">Mobile Money</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment" id="credit_card"
                                        value="credit_card">
                                    <label class="form-check-label" for="credit_card">Credit/Debit Card</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment" id="bank_transfer"
                                        value="bank_transfer">
                                    <label class="form-check-label" for="bank_transfer">Bank Transfer</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Status*</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_status" id="payment_paid"
                                        value="paid" checked>
                                    <label class="form-check-label" for="payment_paid">Paid</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_status" id="payment_pending"
                                        value="pending">
                                    <label class="form-check-label" for="payment_pending">Pending</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Create Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Product Selection Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover" id="productsTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>In Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                        class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="bi bi-box text-muted"></i>
                                                    </div>
                                                @endif
                                                <span class="ms-2">{{ $product->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $product->category }}</td>
                                        <td>UGX {{ number_format($product->price, 0) }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary select-product"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                                Select
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const itemsTable = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
                const noItemsRow = document.getElementById('noItemsRow');
                const addItemButton = document.getElementById('addItem');
                const productModal = new bootstrap.Modal(document.getElementById('productModal'));
                const discountInput = document.getElementById('discount_amount');
                const shippingFeeInput = document.getElementById('shipping_fee');

                let items = [];
                let subtotal = 0;

                // Add Product button clicked - show product selection modal
                addItemButton.addEventListener('click', function () {
                    productModal.show();
                });

                // Product search functionality
                document.getElementById('productSearch').addEventListener('input', function (e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#productsTable tbody tr');

                    rows.forEach(row => {
                        const productName = row.querySelector('td:first-child').textContent.toLowerCase();
                        const productCategory = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                        if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                // Select product button clicked
                document.querySelectorAll('.select-product').forEach(button => {
                    button.addEventListener('click', function () {
                        const productId = this.getAttribute('data-id');
                        const productName = this.getAttribute('data-name');
                        const productPrice = parseFloat(this.getAttribute('data-price'));
                        const productStock = parseInt(this.getAttribute('data-stock'));

                        addProductToOrder(productId, productName, productPrice, productStock);
                        productModal.hide();
                    });
                });

                // Add product to order
                function addProductToOrder(id, name, price, stock) {
                    // Check if product already exists in order
                    const existingItem = items.find(item => item.id === id);

                    if (existingItem) {
                        // Just increment quantity if already in order
                        existingItem.quantity += 1;
                        updateOrderTable();
                    } else {
                        // Add new product to order
                        items.push({
                            id: id,
                            name: name,
                            price: price,
                            stock: stock,
                            quantity: 1
                        });

                        updateOrderTable();
                    }
                }

                // Update order items table
                function updateOrderTable() {
                    // Clear table except for the no items row
                    while (itemsTable.firstChild) {
                        itemsTable.removeChild(itemsTable.firstChild);
                    }

                    if (items.length === 0) {
                        itemsTable.appendChild(noItemsRow);
                        updateTotals();
                        return;
                    }

                    subtotal = 0;

                    items.forEach((item, index) => {
                        const row = document.createElement('tr');
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;

                        row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-center">
                                    <span>${item.name}</span>
                                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm item-quantity" 
                                       name="items[${index}][quantity]" value="${item.quantity}" 
                                       min="1" max="${item.stock}" data-index="${index}">
                            </td>
                            <td>UGX ${item.price.toLocaleString()}</td>
                            <td>UGX ${itemTotal.toLocaleString()}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        `;

                        itemsTable.appendChild(row);
                    });

                    // Add event listeners for the new elements
                    addQuantityEventListeners();
                    addRemoveEventListeners();

                    // Update order totals
                    updateTotals();
                }

                // Add event listeners to quantity inputs
                function addQuantityEventListeners() {
                    document.querySelectorAll('.item-quantity').forEach(input => {
                        input.addEventListener('change', function () {
                            const index = parseInt(this.getAttribute('data-index'));
                            const newQuantity = parseInt(this.value);

                            if (newQuantity < 1) {
                                this.value = 1;
                                items[index].quantity = 1;
                            } else if (newQuantity > items[index].stock) {
                                this.value = items[index].stock;
                                items[index].quantity = items[index].stock;
                            } else {
                                items[index].quantity = newQuantity;
                            }

                            updateOrderTable();
                        });
                    });
                }

                // Add event listeners to remove buttons
                function addRemoveEventListeners() {
                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', function () {
                            const index = parseInt(this.getAttribute('data-index'));
                            items.splice(index, 1);
                            updateOrderTable();
                        });
                    });
                }

                // Update order totals
                function updateTotals() {
                    // Update subtotal display
                    document.getElementById('subtotalDisplay').textContent = `UGX ${subtotal.toLocaleString()}`;
                    document.getElementById('summarySubtotal').textContent = `UGX ${subtotal.toLocaleString()}`;

                    // Calculate total with discount and shipping
                    calculateTotal();
                }

                // Calculate final total
                function calculateTotal() {
                    const discount = parseFloat(discountInput.value) || 0;
                    const shippingFee = parseFloat(shippingFeeInput.value) || 0;

                    const total = Math.max(0, subtotal - discount) + shippingFee;

                    document.getElementById('orderTotal').textContent = `UGX ${total.toLocaleString()}`;
                }

                // Listen for changes to discount and shipping
                discountInput.addEventListener('input', calculateTotal);
                shippingFeeInput.addEventListener('input', calculateTotal);

                // Form validation
                document.getElementById('orderForm').addEventListener('submit', function (e) {
                    if (items.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one product to the order.');
                    }
                });
            });
        </script>
    @endpush
@endsection