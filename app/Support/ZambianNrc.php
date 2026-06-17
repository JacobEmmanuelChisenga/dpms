<?php

namespace App\Support;

/**
 * Zambian National Registration Card (NRC) — NNNNNN/NN/N (9 digits, three slash-separated sections).
 *
 * Example: 123456/78/1
 */
class ZambianNrc
{
    public const PATTERN = '/^\d{6}\/\d{2}\/\d{1}$/';

    public const DISPLAY_HINT = '123456/78/1';

    public const DIGIT_COUNT = 9;

    public static function isValid(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return false;
        }

        $trimmed = trim($value);

        if (preg_match(self::PATTERN, $trimmed) === 1) {
            return true;
        }

        if (! str_contains($trimmed, '/')) {
            return self::normalize($trimmed) !== null;
        }

        return false;
    }

    /**
     * Strip non-digits and apply canonical slashes, or return null when not exactly 9 digits.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (strlen($digits) !== self::DIGIT_COUNT) {
            return null;
        }

        return substr($digits, 0, 6).'/'.substr($digits, 6, 2).'/'.substr($digits, 8, 1);
    }
}
