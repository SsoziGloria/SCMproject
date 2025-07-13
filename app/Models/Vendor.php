<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'revenue',
        'certificationStatus',
        'visitDate',
        'contactPerson',
        'phone',
        'country',
        'address',
        'monthlyRevenue',
        'bankName',
        'accountNumber',
        'complianceStatus',
        'certification'
    ];

    protected $casts = [
        'visitDate' => 'date',
        'revenue' => 'decimal:2',
        'monthlyRevenue' => 'decimal:2'
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
}