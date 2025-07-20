@extends(auth()->user()->role . '.app')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">Supplier Registration</h2>
        <div class="card shadow-sm border-primary">
            <div class="card-body">
                <form action="{{ route('suppliers.register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Supplier Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" name="company" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
@endsection