<?php

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'employee_id',
        'full_name',
        'nrc',
        'department',
        'license_number',
        'license_class',
        'phone',
        'archived_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permits(): HasMany
    {
        return $this->hasMany(Permit::class);
    }

    public function activePermit(): ?Permit
    {
        return $this->permits()
            ->where('status', Permit::STATUS_VALID)
            ->whereDate('expiry_date', '>=', now())
            ->latest('issue_date')
            ->first();
    }

    public function hasActivePermit(): bool
    {
        return $this->activePermit() !== null;
    }

    /**
     * Drivers who may receive a new permit (no current valid, non-expired permit).
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeEligibleForIssuance($query)
    {
        return $query->whereDoesntHave('permits', function ($permitQuery) {
            $permitQuery
                ->where('status', Permit::STATUS_VALID)
                ->whereDate('expiry_date', '>=', now());
        });
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
}
