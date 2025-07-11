<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // Redirect to the welcome page
    public function welcome()
    {
        return view('welcome');
    }
    
    // Show the login form
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // Handle the login POST request
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->except('password'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:supplier,retailer,admin,user',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']), // Ensure consistent email format
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'remember_token' => Str::random(60), // Add remember token for "remember me" functionality
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Optionally, you can send a welcome email or perform other actions here

        return redirect()->route('dashboard');
    }

    // Show the registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Optional: Logout method
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}