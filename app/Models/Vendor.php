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
        'retailer_id',
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
        return $this->hasMany(VendorValidation::class, 'vendor_id', 'vendor_id');
    }

    /**
     * Get the latest validation for this vendor
     */
    public function latestValidation()
    {
        return $this->hasOne(VendorValidation::class, 'vendor_id', 'vendor_id')->latest('validated_at');
    }

    /**
     * Get only valid validations
     */
    public function validValidations(): HasMany
    {
        return $this->hasMany(VendorValidation::class, 'vendor_id', 'vendor_id')->where('is_valid', true);
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
