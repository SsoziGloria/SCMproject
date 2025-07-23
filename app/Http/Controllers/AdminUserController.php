<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use App\Mail\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Order;
use Spatie\Activitylog\Models\Activity;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort options
        $sort = $request->get('sort', 'newest');

        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        }

        $users = $query->paginate(15)->withQueryString();

        // Stats for dashboard cards
        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'supplier' => User::where('role', 'supplier')->count(),
            'retailer' => User::where('role', 'retailer')->count(),
            'customer' => User::where('role', 'customer')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,supplier,retailer,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,other',
            'company_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Hash the password
        $validated['password'] = Hash::make($validated['password']);

        // Create the user
        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Eager load relationships
        $user->loadCount('orders');

        $totalSpent = Order::where('user_id', $user->id)
            ->whereNotNull('delivered_at')
            ->sum('total_amount');

        $orders = $user->orders()->latest()->paginate(10);

        $products = null;
        if ($user->role === 'supplier') {
            $products = $user->products()->latest()->paginate(10);
        }

        $activities = Activity::causedBy($user)
            ->latest() // Get the most recent activities first
            ->paginate(15, ['*'], 'activity_page');

        return view('admin.users.show', compact('user', 'orders', 'products', 'activities', 'totalSpent'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,supplier,retailer,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Update the user
        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'confirmed', Password::defaults()],
            'send_notification' => 'boolean',
        ]);

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Send notification email if requested
        if ($request->has('send_notification') && $request->send_notification) {
            // You can use Laravel's built-in password reset notification
            // or create a custom one

            // Option 1: Custom email
            // Mail::to($user->email)->send(new PasswordReset($user));

            // Option 2: Use Laravel's built-in notification
            $token = Str::random(60);
            $user->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
        }

        // Log activity if you have that feature
        // activity()->performedOn($user)->causedBy(auth()->user())->log('Reset user password');

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Password reset successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating your own account
        if (auth()->id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status'
            ], 403);
        }

        // Update status
        $user->is_active = $request->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_active ?
                "{$user->name} has been activated successfully." :
                "{$user->name} has been deactivated successfully.",
            'status' => $user->is_active
        ]);
    }

    /**
     * Bulk action on users.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $action = $validated['action'];
        $userIds = $validated['user_ids'];

        // Remove current user from the array to prevent self-actions
        $userIds = array_diff($userIds, [auth()->id()]);

        if (empty($userIds)) {
            return back()->with('error', 'No valid users selected for the action.');
        }

        $count = count($userIds);

        switch ($action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = "{$count} users activated successfully.";
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = "{$count} users deactivated successfully.";
                break;

            case 'delete':
                // Handle avatar deletion for each user
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                }

                User::destroy($userIds);
                $message = "{$count} users deleted successfully.";
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Export users data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        // Build query based on filters
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        // Basic export logic - in a real app, you'd use a proper export library like Laravel Excel
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Status', 'Created At']);

            // Add data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
