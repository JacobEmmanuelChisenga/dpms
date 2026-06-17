<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Singleton-style row: official scanned signature PNG/JPEG used on all permit PDFs.
 */
class CertificateSetting extends Model
{
    protected $fillable = [
        'official_signature_path',
    ];

    protected static ?self $singletonCache = null;

    /**
     * One settings row per application (created on first access or upload).
     */
    public static function singleton(): self
    {
        if (static::$singletonCache instanceof self) {
            return static::$singletonCache;
        }

        $row = static::query()->first();
        if ($row === null) {
            $row = static::query()->create(['official_signature_path' => null]);
        }

        static::$singletonCache = $row;

        return static::$singletonCache;
    }

    public static function flushSingletonCache(): void
    {
        static::$singletonCache = null;
    }
}
