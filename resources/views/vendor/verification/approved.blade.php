@extends('supplier.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Verification Approved</h4>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="mb-3">Your account is now fully verified!</h3>
                        <p class="mb-4">
                            Congratulations! Your vendor information has been verified successfully.
                            You now have access to all platform features.
                        </p>

                        <div class="row justify-content-center mt-4">
                            <div class="col-md-10">
                                <div class="card border-success mb-3">
                                    <div class="card-body">
                                        <h5>Vendor Information Summary</h5>
                                        <table class="table table-sm table-borderless text-start">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Company:</strong></td>
                                                    <td>{{ $vendor->company_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Contact Person:</strong></td>
                                                    <td>{{ $vendor->contact_person }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td>{{ $vendor->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Verification Date:</strong></td>
                                                    <td>{{ $vendor->updated_at->format('F j, Y') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            @if(auth()->user()->role === 'supplier')
                                <a href="{{ route('supplier.products.index') }}" class="btn btn-primary">Manage Your
                                    Products</a>
                            @elseif(auth()->user()->role === 'retailer')
                                <a href="{{ route('retailer.dashboard') }}" class="btn btn-primary">Go to Retailer Dashboard</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection