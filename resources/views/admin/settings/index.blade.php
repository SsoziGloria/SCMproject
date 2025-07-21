@extends('admin.app')

@php
use App\Models\Setting;
@endphp

@section('content')
<div class="container-fluid h-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supplier Settings</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <!-- Global Supplier Settings Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Global Supplier Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.toggle-supplier-products') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p>Control visibility of <strong>all supplier products</strong> in the shop.</p>
                                <p class="mb-0">
                                    Current global status:
                                    <span class="badge bg-{{ $globalSupplierSetting ? 'success' : 'danger' }}">
                                        {{ $globalSupplierSetting ? 'Visible' : 'Hidden' }}
                                    </span>
                                </p>
                            </div>
                            <button type="submit" class="btn btn-{{ $globalSupplierSetting ? 'warning' : 'success' }}">
                                <i class="bi bi-{{ $globalSupplierSetting ? 'eye-slash' : 'eye' }}"></i>
                                {{ $globalSupplierSetting ? 'Hide All Supplier Products' : 'Show All Supplier Products'
                                }}
                            </button>
                        </div>
                    </form>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> This is the default setting. Individual supplier settings
                        below will not override this.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Supplier Settings Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Individual Supplier Settings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($suppliers as $supplier)
                        <div class="list-group-item">
                            <form action="{{ route('admin.settings.toggle-supplier-products') }}" method="POST">
                                @csrf
                                <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $supplier->name }}</h6>
                                        <p class="mb-0 small text-muted">
                                            {{ $supplier->email }} |
                                            Status:
                                            <span
                                                class="badge bg-{{ $supplier->user->is_active ? 'success' : 'danger' }}">
                                                {{ $supplier->user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="me-3 badge bg-{{ $supplierSettings[$supplier->supplier_id]['visible'] ? 'success' : 'danger' }}">
                                            {{ $supplierSettings[$supplier->supplier_id]['visible'] ? 'Visible' :
                                            'Hidden' }}
                                        </span>
                                        <button type="submit"
                                            class="btn btn-sm btn-{{ $supplierSettings[$supplier->supplier_id]['visible'] ? 'warning' : 'success' }}">
                                            <i
                                                class="bi bi-{{ $supplierSettings[$supplier->supplier_id]['visible'] ? 'eye-slash' : 'eye' }}"></i>
                                            {{ $supplierSettings[$supplier->supplier_id]['visible'] ? 'Hide' : 'Show' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach

                        @if(count($suppliers) === 0)
                        <div class="list-group-item text-center py-4">
                            <p class="mb-0 text-muted">No suppliers found.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Other Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
</div>
@endsection