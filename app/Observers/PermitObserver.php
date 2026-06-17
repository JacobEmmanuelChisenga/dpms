<?php

namespace App\Observers;

use App\Models\Permit;
use App\Support\AuditLogger;
use DateTimeInterface;

class PermitObserver
{
    /** @var list<string> */
    private const LOGGED_KEYS = [
        'driver_id',
        'permit_number',
        'issue_date',
        'expiry_date',
        'status',
        'issued_by',
        'qr_code',
    ];

    public function created(Permit $permit): void
    {
        AuditLogger::fromRequest($permit, 'created', [
            'snapshot' => $permit->only(self::LOGGED_KEYS),
        ]);
    }

    public function updated(Permit $permit): void
    {
        $changes = [];

        foreach (self::LOGGED_KEYS as $key) {
            if (! $permit->wasChanged($key)) {
                continue;
            }

            $from = $this->normalizeValue($permit->getOriginal($key));
            $to = $this->normalizeValue($permit->getAttribute($key));

            $changes[$key] = [
                'from' => $from,
                'to' => $to,
            ];
        }

        if ($changes === []) {
            return;
        }

        AuditLogger::fromRequest($permit, 'updated', ['changes' => $changes]);
    }

    public function deleted(Permit $permit): void
    {
        AuditLogger::fromRequest($permit, 'deleted', [
            'snapshot' => $permit->only(['id', 'permit_number', 'driver_id', 'status']),
        ]);
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return $value;
    }
}
