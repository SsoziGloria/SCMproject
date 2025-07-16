<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;

class EnsureVendorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user role requires verification
        if ($user->role === 'supplier' || $user->role === 'retailer') {
            // Check if vendor is verified
            $vendor = Vendor::where('supplier_id', $user->id)
                ->where('validation_status', 'Approved')
                ->first();

            if (!$vendor) {
                return redirect()->route('vendor.verification.form')
                    ->with('error', 'You must complete vendor verification before accessing this area.');
            }
        }
        return $next($request);
    }
}
