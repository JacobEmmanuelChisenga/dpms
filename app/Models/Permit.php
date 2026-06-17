<?php

namespace App\Models;

use Database\Factories\PermitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $issue_date
 * @property Carbon $expiry_date
 */
class Permit extends Model
{
    /** @use HasFactory<PermitFactory> */
    use HasFactory;

    public const STATUS_VALID = 'valid';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REVOKED = 'revoked';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'driver_id',
        'permit_number',
        'issue_date',
        'expiry_date',
        'status',
        'issued_by',
        'qr_code',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function syncStatusFromDates(): bool
    {
        if ($this->status === self::STATUS_REVOKED) {
            return false;
        }

        if ($this->expiry_date->lt(now()->startOfDay())) {
            return $this->fill(['status' => self::STATUS_EXPIRED])->isDirty()
                ? $this->save()
                : false;
        }

        if ($this->status !== self::STATUS_VALID) {
            return $this->fill(['status' => self::STATUS_VALID])->save();
        }

        return false;
    }
}
