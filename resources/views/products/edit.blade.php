@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Title -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit: {{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-eye"></i> View Product
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="mb-3">Basic Information</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="product_id" class="form-label">Product Code *</label>
                                        <input type="text" class="form-control @error('product_id') is-invalid @enderror"
                                            id="product_id" name="product_id"
                                            value="{{ old('product_id', $product->product_id) }}" required>
                                        @error('product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Unique identifier for this product</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="price" class="form-label">Price (UGX) *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">UGX</span>
                                            <input type="number" step="0.01"
                                                class="form-control @error('price') is-invalid @enderror" id="price"
                                                name="price" value="{{ old('price', $product->price) }}" required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                            id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select @error('category') is-invalid @enderror" id="category"
                                            name="category">
                                            <option value="">Select Category</option>
                                            <option value="Dark Chocolate" {{ (old('category', $product->category) == 'Dark Chocolate') ? 'selected' : '' }}>Dark Chocolate</option>
                                            <option value="Milk Chocolate" {{ (old('category', $product->category) == 'Milk Chocolate') ? 'selected' : '' }}>Milk Chocolate</option>
                                            <option value="White Chocolate" {{ (old('category', $product->category) == 'White Chocolate') ? 'selected' : '' }}>White Chocolate</option>
                                            <option value="Truffles" {{ (old('category', $product->category) == 'Truffles') ? 'selected' : '' }}>Truffles</option>
                                            <option value="Bars" {{ (old('category', $product->category) == 'Bars') ? 'selected' : '' }}>Bars</option>
                                            <option value="Gift Sets" {{ (old('category', $product->category) == 'Gift Sets') ? 'selected' : '' }}>Gift Sets</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_id" class="form-label">Supplier</label>
                                        <select class="form-select @error('supplier_id') is-invalid @enderror"
                                            id="supplier_id" name="supplier_id">
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->vendor_id }}" {{ (old('supplier_id', $product->supplier_id) == $supplier->vendor_id) ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <h5 class="mb-3">Product Details</h5>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description"
                                        rows="4">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="ingredients" class="form-label">Ingredients</label>
                                    <textarea class="form-control @error('ingredients') is-invalid @enderror"
                                        id="ingredients" name="ingredients"
                                        rows="3">{{ old('ingredients', $product->ingredients) }}</textarea>
                                    @error('ingredients')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">Product Image</h5>
                                </div>
                                <div class="card-body">
                                    <div class="image-preview mb-3 text-center">
                                        @if($product->image)
                                            <img id="imagePreview" src="{{ asset('storage/' . $product->image) }}"
                                                class="img-fluid rounded" alt="{{ $product->name }}"
                                                style="max-height: 200px; width: auto;">
                                        @else
                                            <img id="imagePreview" src="{{ asset('images/placeholder-image.png') }}"
                                                class="img-fluid rounded" alt="Product image placeholder"
                                                style="max-height: 200px; width: auto;">
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Update Image</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            id="image" name="image" accept="image/*">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Recommended size: 800x800px. Max: 2MB</div>
                                    </div>

                                    @if($product->image)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_image"
                                                name="remove_image" value="1">
                                            <label class="form-check-label" for="remove_image">
                                                Remove existing image
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">Options</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured"
                                            value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featured">
                                            Featured Product
                                        </label>
                                        <div class="form-text">Display in featured products section</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">Meta Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Created:</strong></p>
                                        <p>{{ $product->created_at->format('M d, Y H:i') }}</p>
                                    </div>

                                    <div class="mb-0">
                                        <p class="mb-1"><strong>Last Updated:</strong></p>
                                        <p class="mb-0">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Update Product
                                        </button>
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Image preview functionality
            document.getElementById('image').addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('imagePreview').setAttribute('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Handle remove image checkbox
            const removeImageCheckbox = document.getElementById('remove_image');
            if (removeImageCheckbox) {
                removeImageCheckbox.addEventListener('change', function () {
                    const imageInput = document.getElementById('image');
                    if (this.checked) {
                        imageInput.disabled = true;
                    } else {
                        imageInput.disabled = false;
                    }
                });
            }
        });
    </script>
@endpush