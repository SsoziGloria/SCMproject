<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     */
    public function show()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('admin.profile', compact('user'));
        } elseif ($user->role === 'retailer') {
            return view('retailer.profile', compact('user'));
        } elseif ($user->role === 'supplier') {
            return view('supplier.profile', compact('user'));
        } else {
            return view('user.profile', compact('user'));
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'fullName' => 'required|string|max:255',
            'about' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'certification_status' => 'nullable|in:pending,approved,rejected',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Update fields
        $user->name = $data['fullName'];
        $user->about = $data['about'] ?? null;
        $user->country = $data['country'] ?? null;
        $user->phone = $data['phone'] ?? null;
        $user->email = $data['email'];
        $user->twitter = $data['twitter'] ?? null;
        $user->facebook = $data['facebook'] ?? null;
        $user->instagram = $data['instagram'] ?? null;
        $user->linkedin = $data['linkedin'] ?? null;
        $user->certification_status = $data['certification_status'] ?? $user->certification_status;


        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}