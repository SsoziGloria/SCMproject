@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center"><i class="bi bi-plus-circle"></i> Add Inventory Adjustment</h2>
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-info">
                <div class="card-body">
                    <form action="{{ route('inventories.adjustments.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="inventory_id" class="form-label">Inventory Item</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-box"></i></span>
                                <select name="inventory_id" id="inventory_id" class="form-select" required>
                                    <option value="">Select Item</option>
                                    @foreach($inventories as $inventory)
                                        <option value="{{ $inventory->id }}">
                                            {{ $inventory->product_name }} (Current: {{ $inventory->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-arrow-left-right"></i></span>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="add">Add</option>
                                    <option value="remove">Remove</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-123"></i></span>
                                <input type="number" name="amount" id="amount" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                <input type="text" name="reason" id="reason" class="form-control" placeholder="Optional">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-info px-4">
                                <i class="bi bi-check-circle"></i> Submit Adjustment
                            </button>
                            <a href="{{ route('inventories.adjustments') }}" class="btn btn-secondary ms-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection