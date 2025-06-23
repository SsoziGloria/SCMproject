@extends('admin.app')

@section('title', ucfirst($role) . ' Users')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">
            @if($role === 'user')
                Customer Table
            @elseif($role === 'retailer')
                Retailer Table
            @elseif($role === 'supplier')
                Supplier Table
            @elseif($role === 'admin')
                Admin Table
            @else
                {{ ucfirst($role) }} Users
            @endif
        </h2>
        {{-- Users table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Email Verified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="text-success">✔ Verified</span>
                                        @else
                                            <span class="text-warning">✖ Not Verified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection