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

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role === 'supplier') {
            $vendor = Vendor::where('supplier_id', $user->id)->first();
        } else { // retailer
            $vendor = Vendor::where('retailer_id', $user->id)->first();
        }

        if (!$vendor) {
            return redirect()->route('vendor.verification.form')
                ->with('error', 'You must complete vendor verification before accessing this area.');
        }

        if ($vendor->validation_status === 'Pending') {
            return redirect()->route('vendor.verification.pending')
                ->with('info', 'Your vendor verification is still pending approval.');
        } elseif ($vendor->validation_status === 'Rejected') {
            return redirect()->route('vendor.verification.form')
                ->with('error', 'Your vendor verification was rejected. Please submit again.');
        } elseif ($vendor->validation_status !== 'Approved') {
            // Any other status (or null/empty status)
            return redirect()->route('vendor.verification.form')
                ->with('error', 'Your vendor verification status is invalid. Please contact support.');
        }

        return $next($request);
    }
}
