<?php

namespace App\Services;

use App\Models\Permit;

class PermitNumberGenerator
{
    public static function generate(): string
    {
        do {
            $number = 'DPMS-'.now()->format('Y').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Permit::where('permit_number', $number)->exists());

        return $number;
    }
}
