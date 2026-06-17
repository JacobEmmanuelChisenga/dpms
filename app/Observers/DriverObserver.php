<?php

namespace App\Observers;

use App\Models\Driver;
use App\Support\AuditLogger;

class DriverObserver
{
    /** @var list<string> */
    private const LOGGED_KEYS = [
        'employee_id',
        'full_name',
        'nrc',
        'department',
        'license_number',
        'license_class',
        'phone',
        'archived_at',
    ];

    public function created(Driver $driver): void
    {
        AuditLogger::fromRequest($driver, 'created', [
            'snapshot' => $driver->only(self::LOGGED_KEYS),
        ]);
    }

    public function updated(Driver $driver): void
    {
        $changes = [];

        foreach (self::LOGGED_KEYS as $key) {
            if ($driver->wasChanged($key)) {
                $changes[$key] = [
                    'from' => $driver->getRawOriginal($key),
                    'to' => $driver->getAttribute($key),
                ];
            }
        }

        if ($changes === []) {
            return;
        }

        AuditLogger::fromRequest($driver, 'updated', ['changes' => $changes]);
    }

    public function deleted(Driver $driver): void
    {
        AuditLogger::fromRequest($driver, 'deleted', [
            'snapshot' => $driver->only([
                'id',
                'employee_id',
                'full_name',
                'department',
                'nrc',
            ]),
        ]);
    }
}
