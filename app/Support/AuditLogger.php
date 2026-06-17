<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public static function record(
        ?Authenticatable $actor,
        Model $subject,
        string $event,
        array $properties = []
    ): void {
        $userId = null;
        if ($actor instanceof User) {
            $userId = $actor->getKey();
        } elseif ($actor !== null && method_exists($actor, 'getKey')) {
            $userId = $actor->getKey();
        } else {
            $userId = Auth::id();
        }

        AuditLog::create([
            'user_id' => $userId,
            'event' => $event,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'properties' => $properties ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'logged_at' => now(),
        ]);
    }

    /** @param  array<string, mixed>  $properties */
    public static function fromRequest(Model $subject, string $event, array $properties = []): void
    {
        $user = Auth::user();
        $actor = $user instanceof User ? $user : null;
        static::record($actor, $subject, $event, $properties);
    }
}
