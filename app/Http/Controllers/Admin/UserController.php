<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // List all users
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderByRaw("FIELD(role, 'user', 'retailer', 'supplier', 'admin')")
            ->paginate(15);

        return view('admin.user', compact('users'));
    }

    // Show edit form
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin,supplier,retailer',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function byRole(Request $request, $role)
    {
        $users = User::where('role', $role)
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.roles', compact('users', 'role'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,supplier,retailer',
            'is_active' => 'required|boolean',
            'profile_photo' => 'nullable|image|max:1024',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'twitter' => 'nullable|string|max:50',
            'facebook' => 'nullable|string|max:50',
            'instagram' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string|max:50',
        ]);

        $userData = $request->except(['profile_photo', 'password_confirmation', 'send_credentials', 'force_password_reset']);
        $userData['password'] = Hash::make($request->password);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $userData['profile_photo'] = $path;
        }

        $user = User::create($userData);

        // Handle sending credentials email if selected
        if ($request->has('send_credentials')) {
            // You can dispatch an email job here
            // Mail::to($user->email)->send(new NewUserCredentials($user, $request->password));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

}