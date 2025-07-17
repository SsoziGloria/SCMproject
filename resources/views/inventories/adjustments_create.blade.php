@extends('supplier.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Create Inventory Adjustment</h4>
                            <a href="{{ route('inventories.adjustments') }}" class="btn btn-sm btn-light">
                                <i class="bi bi-arrow-left"></i> Back to Adjustments
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('inventories.adjustments.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="inventory_id" class="form-label">Select Product Inventory</label>
                                <select name="inventory_id" id="inventory_id" class="form-control" required>
                                    <option value="">Select an inventory item</option>
                                    @foreach($inventories as $inventory)
                                        <option value="{{ $inventory->id }}" data-current="{{ $inventory->quantity }}"
                                            data-unit="{{ $inventory->unit }}">
                                            {{ $inventory->product_name }}
                                            (Current: {{ $inventory->quantity }} {{ $inventory->unit }}, Location:
                                            {{ $inventory->location }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="current_quantity" class="form-label">Current Quantity</label>
                                    <div class="input-group">
                                        <input type="text" id="current_quantity" class="form-control" readonly>
                                        <span class="input-group-text" id="current_unit">pcs</span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="adjustment_type" class="form-label">Adjustment Type</label>
                                    <select name="adjustment_type" id="adjustment_type" class="form-control" required>
                                        <option value="">Select adjustment type</option>
                                        <option value="increase">Increase (Add Stock)</option>
                                        <option value="decrease">Decrease (Remove Stock)</option>
                                        <option value="correction">Correction (Count Error)</option>
                                        <option value="damage">Damage Write-off</option>
                                        <option value="expiry">Expiry Write-off</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity_change" class="form-label">Quantity Change</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="change_symbol">±</span>
                                    <input type="number" name="quantity_change" id="quantity_change" class="form-control"
                                        min="1" required>
                                    <span class="input-group-text" id="change_unit">pcs</span>
                                </div>
                                <div class="form-text">Enter a positive number. The sign will be applied based on the
                                    adjustment type.</div>
                            </div>

                            <div class="mb-3">
                                <label for="new_quantity" class="form-label">New Quantity (Result)</label>
                                <div class="input-group">
                                    <input type="text" id="new_quantity" class="form-control bg-light" readonly>
                                    <span class="input-group-text" id="new_unit">pcs</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Adjustment</label>
                                <textarea name="reason" id="reason" rows="3" class="form-control" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control"></textarea>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-4">Submit Adjustment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inventorySelect = document.getElementById('inventory_id');
                const currentQuantity = document.getElementById('current_quantity');
                const currentUnit = document.getElementById('current_unit');
                const adjustmentType = document.getElementById('adjustment_type');
                const changeSymbol = document.getElementById('change_symbol');
                const quantityChange = document.getElementById('quantity_change');
                const changeUnit = document.getElementById('change_unit');
                const newQuantity = document.getElementById('new_quantity');
                const newUnit = document.getElementById('new_unit');

                // Update current quantity when inventory selection changes
                inventorySelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const current = selectedOption.getAttribute('data-current');
                        const unit = selectedOption.getAttribute('data-unit');

                        currentQuantity.value = current;
                        currentUnit.textContent = unit;
                        changeUnit.textContent = unit;
                        newUnit.textContent = unit;

                        updateNewQuantity();
                    } else {
                        currentQuantity.value = '';
                        currentUnit.textContent = 'pcs';
                        changeUnit.textContent = 'pcs';
                        newUnit.textContent = 'pcs';
                        newQuantity.value = '';
                    }
                });

                // Update symbol when adjustment type changes
                adjustmentType.addEventListener('change', function () {
                    if (this.value === 'increase') {
                        changeSymbol.textContent = '+';
                    } else if (this.value === 'decrease' || this.value === 'damage' || this.value === 'expiry') {
                        changeSymbol.textContent = '-';
                    } else {
                        changeSymbol.textContent = '±';
                    }

                    updateNewQuantity();
                });

                // Update new quantity when quantity change input changes
                quantityChange.addEventListener('input', updateNewQuantity);

                // Calculate new quantity
                function updateNewQuantity() {
                    const current = parseInt(currentQuantity.value) || 0;
                    const change = parseInt(quantityChange.value) || 0;

                    if (!change || !adjustmentType.value) {
                        newQuantity.value = current;
                        return;
                    }

                    let result = current;

                    if (adjustmentType.value === 'increase') {
                        result = current + change;
                    } else if (adjustmentType.value === 'decrease' || adjustmentType.value === 'damage' || adjustmentType.value === 'expiry') {
                        result = current - change;
                        if (result < 0) {
                            newQuantity.value = 'Error: Cannot go below 0';
                            newQuantity.classList.add('is-invalid');
                            return;
                        }
                    } else if (adjustmentType.value === 'correction') {
                        // For correction, the change is the absolute difference
                        result = change;
                    }

                    newQuantity.value = result;
                    newQuantity.classList.remove('is-invalid');
                }
            });
        </script>
    @endpush

@endsection