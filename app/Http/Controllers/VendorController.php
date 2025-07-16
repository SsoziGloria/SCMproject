<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the vendor verification form
     */
    public function showVerificationForm()
    {
        // Check if user already has a vendor record
        $vendor = Vendor::where('supplier_id', Auth::id())->first();

        if ($vendor) {
            if ($vendor->validation_status === 'Approved') {
                return $this->showApprovedStatus($vendor);
            } else {
                return $this->showPendingStatus($vendor);
            }
        }

        return view('vendor.verification.form');
    }

    /**
     * Store vendor verification details
     */
    public function storeVerification(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'contact_person' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'monthly_revenue' => 'nullable|numeric',
            'revenue' => 'sometimes|numeric',
            'certification' => 'nullable|string|max:100',
            'verification_document' => 'required|file|mimes:pdf|max:5120',
            'agreement' => 'required|accepted',
        ]);

        // Store PDF document
        $pdfPath = null;
        if ($request->hasFile('verification_document')) {
            $fileName = 'vendor_' . Auth::id() . '_' . time() . '.pdf';
            $pdfPath = $request->file('verification_document')->storeAs(
                'vendor_documents',
                $fileName,
                'public'
            );
        }

        // Create vendor record
        Vendor::create([
            'name' => Auth::user()->name,
            'email' => $validated['email'],
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['contact_person'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'country' => $validated['country'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'monthly_revenue' => null,
            'revenue' => null,
            'certification' => null,
            'pdf_path' => $pdfPath,
            'supplier_id' => Auth::id(),
            'visit_date' => Carbon::now()->addDays(rand(2, 7)),
        ]);

        // If user is supplier but not in suppliers table, create record there too
        if (Auth::user()->role === 'supplier') {
            $supplierExists = Supplier::where('supplier_id', Auth::id())->exists();
            if (!$supplierExists) {
                Supplier::create([
                    'user_id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'status' => 'pending'
                ]);
            }
        }

        // Send to external validation service if needed
        // Call your validation service here...
        // $this->sendToExternalValidation($vendor);

        return redirect()->route('vendor.verification.pending')
            ->with('success', 'Your verification details have been submitted successfully. We will review your application shortly.');
    }

    /**
     * Show pending verification status
     */
    public function showPendingStatus(Vendor $vendor = null)
    {
        if (!$vendor) {
            $vendor = Vendor::where('supplier_id', Auth::id())->first();

            if (!$vendor) {
                return redirect()->route('vendor.verification.form');
            }
        }

        return view('vendor.verification.pending', compact('vendor'));
    }

    /**
     * Show approved verification status
     */
    public function showApprovedStatus(Vendor $vendor = null)
    {
        if (!$vendor) {
            $vendor = Vendor::where('supplier_id', Auth::id())->first();

            if (!$vendor || $vendor->validation_status !== 'Approved') {
                return redirect()->route('vendor.verification.pending');
            }
        }

        return view('vendor.verification.approved', compact('vendor'));
    }

    /**
     * Redirect to external validation page
     */
    public function redirectToExternalValidation(Request $request, $vendorId)
    {
        // This would redirect to your existing external validation system
        $vendor = Vendor::findOrFail($vendorId);

        // Construct the URL to your validation system
        $validationUrl = route('vendor-validation', [
            'vendor_id' => $vendor->vendor_id,
            'document' => $vendor->pdf_path,
            'return_url' => route('vendor.verification.pending')
        ]);

        return redirect()->to($validationUrl);
    }
}