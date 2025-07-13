@extends('admin.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">Vendor Document Validation History</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.vendor-validation') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> New Validation
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Vendor</th>
                                <th>Document</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($validations as $validation)
                                <tr>
                                    <td>{{ $validation->id }}</td>
                                    <td>{{ $validation->vendor->name }}</td>
                                    <td>{{ $validation->original_filename }}</td>
                                    <td>
                                        @if($validation->is_valid)
                                            <span class="badge bg-success">Valid</span>
                                        @else
                                            <span class="badge bg-danger">Invalid</span>
                                        @endif
                                    </td>
                                    <td>{{ $validation->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.vendor-validation.download', $validation->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                data-bs-target="#detailsModal{{ $validation->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No validation records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $validations->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modals -->
    @foreach($validations as $validation)
        <div class="modal fade" id="detailsModal{{ $validation->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Validation Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Vendor:</strong> {{ $validation->vendor->name }}</p>
                        <p><strong>Document:</strong> {{ $validation->original_filename }}</p>
                        <p><strong>Status:</strong>
                            @if($validation->is_valid)
                                <span class="text-success">Valid</span>
                            @else
                                <span class="text-danger">Invalid</span>
                            @endif
                        </p>
                        <p><strong>Message:</strong> {{ $validation->validation_message }}</p>
                        <p><strong>Date:</strong> {{ $validation->created_at->format('M d, Y H:i:s') }}</p>

                        @if($validation->validation_details)
                            <h6 class="mt-3">Validation Checks:</h6>
                            <ul>
                                @foreach($validation->validation_details as $check => $result)
                                    <li class="{{ $result ? 'text-success' : 'text-danger' }}">
                                        {{ $result ? '✓' : '✗' }} {{ $check }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.vendor-validation.download', $validation->id) }}" class="btn btn-primary">
                            Download Document
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection