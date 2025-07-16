<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use App\Models\VendorValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'vendor_id';
    protected $fillable = [
        'name',
        'email',
        'company_name',
        'contact_person',
        'phone',
        'address',
        'bank_name',
        'account_number',
        'certification',
        'certification_status',
        'compliance_status',
        'monthly_revenue',
        'revenue',
        'country',
        'financial_score',
        'regulatory_compliance',
        'reputation',
        'validation_status',
        'visit_date',
        'pdf_path',
        'supplier_id',
    ];

    protected $casts = [
        'visitDate' => 'date',
        'revenue' => 'decimal:0',
        'monthlyRevenue' => 'decimal:0'
    ];

    /**
     * Get all validations for this vendor
     */
    public function validations(): HasMany
    {
        return $this->hasMany(VendorValidation::class);
    }

    /**
     * Get the latest validation for this vendor
     */
    public function latestValidation()
    {
        return $this->hasOne(VendorValidation::class)->latest('validated_at');
    }

    /**
     * Get only valid validations
     */
    public function validValidations(): HasMany
    {
        return $this->hasMany(VendorValidation::class)->where('is_valid', true);
    }

    /**
     * Check if vendor has valid documents
     */
    public function hasValidDocuments(): bool
    {
        return $this->validValidations()->exists();
    }

    /**
     * Get the most recent validation status
     */
    public function getLatestValidationStatusAttribute(): ?bool
    {
        return $this->latestValidation?->is_valid;
    }

    /**
     * Get validation statistics
     */
    public function getValidationStatsAttribute(): array
    {
        $validations = $this->validations;

        return [
            'total' => $validations->count(),
            'valid' => $validations->where('is_valid', true)->count(),
            'invalid' => $validations->where('is_valid', false)->count(),
            'latest_validation_date' => $this->latestValidation?->validated_at
        ];
    }

    /**
     * Scope for vendors with valid documents
     */
    public function scopeWithValidDocuments($query)
    {
        return $query->whereHas('validValidations');
    }

    /**
     * Scope for vendors without valid documents
     */
    public function scopeWithoutValidDocuments($query)
    {
        return $query->whereDoesntHave('validValidations');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get verification status badge
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->validation_status) {
            case 'Approved':
                return '<span class="badge bg-success">Approved</span>';
            case 'Rejected':
                return '<span class="badge bg-danger">Rejected</span>';
            default:
                return '<span class="badge bg-warning text-dark">Pending</span>';
        }
    }

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
            'account_number' => 'required|string|max:50',
            'monthly_revenue' => 'required|numeric',
            'revenue' => 'nullable|numeric',
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

        // Create or update vendor record
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
                'monthly_revenue' => $validated['monthly_revenue'],
                'revenue' => $validated['revenue'],
                'certification' => $validated['certification'],
                'pdf_path' => $pdfPath,
                'supplier_id' => Auth::id(),
                'validation_status' => 'Pending',
                'visit_date' => Carbon::now()->addDays(rand(2, 7)),
            ]
        );

        // If user is supplier but not in suppliers table, create record there too
        if (Auth::user()->role === 'supplier') {
            $supplier = Supplier::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'name' => $validated['company_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'status' => 'pending'
                ]
            );
        }

        // Create notification for admin about new vendor verification
        // You'll need a Notification class for this
        // Notification::create([
        //     'type' => 'vendor_verification',
        //     'title' => 'New Vendor Verification',
        //     'message' => Auth::user()->name . ' has submitted verification documents',
        //     'data' => json_encode(['vendor_id' => $vendor->vendor_id]),
        //     'is_read' => false
        // ]);

        return redirect()->route('vendor.verification.pending')
            ->with('success', 'Your verification details have been submitted successfully.');
    }

    /**
     * Trigger document validation in background
     */
    private function triggerValidation($vendorId, $pdfPath)
    {
        // Create a request to your validation API
        $response = Http::attach(
            'file',
            Storage::disk('public')->get($pdfPath),
            basename($pdfPath)
        )->post(route('api.vendor-validation.validate'), [
                    'vendor_id' => $vendorId
                ]);

        return $response->json();
    }
}