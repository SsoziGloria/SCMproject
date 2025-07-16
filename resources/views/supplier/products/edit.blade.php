@extends('supplier.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Product</h1>
            <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('supplier.products.update', $product->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">Product ID/SKU*</label>
                            <input type="text" class="form-control" id="product_id" name="product_id"
                                value="{{ old('product_id', $product->product_id) }}" required>
                            <small class="text-muted">Unique identifier for your product</small>
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name*</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $product->name) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price (UGX)*</label>
                            <div class="input-group">
                                <span class="input-group-text">UGX</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
                                    value="{{ old('price', $product->price) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label">Stock*</label>
                            <input type="number" min="0" class="form-control" id="stock" name="stock"
                                value="{{ old('stock', $product->stock) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="description" class="form-label">Product Description</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label for="ingredients" class="form-label">Ingredients</label>
                        <textarea class="form-control" id="ingredients" name="ingredients"
                            rows="3">{{ old('ingredients', $product->ingredients) }}</textarea>
                        <small class="text-muted">List all ingredients separated by commas</small>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">Product Image</label>
                        <div class="row">
                            <div class="col-md-6">
                                @if($product->image)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($product->image) }}" class="img-thumbnail"
                                            style="max-height: 200px;">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                        <label class="form-check-label text-danger" for="remove_image">
                                            Remove current image
                                        </label>
                                    </div>
                                @else
                                    <p class="text-muted mb-3">No image uploaded</p>
                                @endif

                                <input class="form-control" type="file" id="image" name="image" accept="image/*">
                                <div class="mt-2" id="image-preview"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="featured">
                            Featured Product (will appear in highlights)
                        </label>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <a href="{{ route('supplier.products.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function (event) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail mt-2" style="height: 200px;">`;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
@endsection