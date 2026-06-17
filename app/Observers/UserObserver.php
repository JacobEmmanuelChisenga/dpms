<?php

namespace App\Observers;

use App\Models\User;
use App\Support\AuditLogger;

class UserObserver
{
    public function created(User $user): void
    {
        AuditLogger::fromRequest($user, 'created', [
            'snapshot' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }

    public function updated(User $user): void
    {
        if (! $user->wasChanged(['name', 'email', 'role'])) {
            return;
        }

        $changes = [];

        foreach (['name', 'email', 'role'] as $key) {
            if ($user->wasChanged($key)) {
                $changes[$key] = [
                    'from' => $user->getOriginal($key),
                    'to' => $user->getAttribute($key),
                ];
            }
        }

        AuditLogger::fromRequest($user, 'updated', ['changes' => $changes]);
    }

    /**
     * Fired before hard delete — morph id still persisted on audit row.
     */
    public function deleted(User $user): void
    {
        AuditLogger::fromRequest($user, 'deleted', [
            'snapshot' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }
}
