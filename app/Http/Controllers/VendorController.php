<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Namu\WireChat\Models\Conversation;

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
        $fileName = null;
        if ($request->hasFile('verification_document')) {
            $disk = config('vendor_validation.storage_disk', 'vendor_docs');

            $fileName = 'vendor_' . Auth::id() . '_' . time() . '.pdf';

            $request->file('verification_document')->storeAs(
                '/',
                $fileName,
                $disk
            );
        }

        // Create vendor record
        if (Auth::user()->role === 'supplier') {
            $vendor = Vendor::updateOrCreate(
                ['supplier_id' => Auth::id()],
                [
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
                    'pdf_path' => $fileName,
                    'validation_status' => 'Pending',
                    'visit_date' => Carbon::now()->addDays(rand(2, 7)),
                ]
            );

            $supplierExists = Supplier::where('supplier_id', Auth::id())->exists();
            if (!$supplierExists) {
                Supplier::create([
                    'supplier_id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'company' => $validated['company_name'],
                    'status' => 'pending'
                ]);
            }
        } else {
            $vendor = Vendor::create([
                'name' => Auth::user()->name,
                'email' => $validated['email'],
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['contact_person'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'country' => $validated['country'],
                'bank_name' => $validated['bank_name'],
                'account_number' => null,
                'monthly_revenue' => null,
                'revenue' => null,
                'certification' => null,
                'pdf_path' => $fileName,
                'retailer_id' => Auth::id(),
                'visit_date' => Carbon::now()->addDays(rand(2, 7)),
            ]);
        }

        $systemUser = User::where('email', 'system@chocolatescm')->first();
        $adminConversation = Conversation::where('id', '1')->first();

        if ($systemUser && $adminConversation) {
            $conversation = $systemUser->conversations()->first();

            $message = $systemUser->sendMessageTo($adminConversation, "A new vendor application has been submitted: '{$vendor->name}'. Please review for approval.");
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
    public function showPendingStatus(Vendor $vendor)
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
    public function showApprovedStatus(Vendor $vendor)
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
