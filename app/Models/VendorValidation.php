<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VendorValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'original_filename',
        'file_path',
        'file_size',
        'is_valid',
        'validation_results',
        'validation_message',
        'validated_at'
    ];

    protected $casts = [
        'validation_results' => 'array',
        'is_valid' => 'boolean',
        'validated_at' => 'datetime',
        'file_size' => 'integer'
    ];

    /**
     * Get the vendor that owns the validation
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    /**
     * Get the file URL for download
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get human readable file size
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get validation status with color
     */
    public function getStatusBadgeAttribute(): array
    {
        return $this->is_valid ? [
            'text' => 'Valid',
            'color' => 'success',
            'icon' => 'check-circle'
        ] : [
            'text' => 'Invalid',
            'color' => 'danger',
            'icon' => 'x-circle'
        ];
    }

    /**
     * Get validation results summary
     */
    public function getValidationSummaryAttribute(): array
    {
        $results = $this->validation_results ?? [];

        return [
            'total_checks' => count($results),
            'passed_checks' => count(array_filter($results, fn($value) => $value === true)),
            'failed_checks' => count(array_filter($results, fn($value) => $value === false))
        ];
    }

    /**
     * Scope for valid validations
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope for invalid validations
     */
    public function scopeInvalid($query)
    {
        return $query->where('is_valid', false);
    }

    /**
     * Scope for recent validations
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('validated_at', '>=', now()->subDays($days));
    }
}