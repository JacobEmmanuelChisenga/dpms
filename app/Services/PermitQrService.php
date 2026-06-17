<?php

namespace App\Services;

use App\Models\Permit;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PermitQrService
{
    public static function verificationUrl(Permit $permit): string
    {
        return route('permits.verify', ['code' => $permit->permit_number], absolute: true);
    }

    /**
     * Ensure a QR image exists for the permit (writes to public disk).
     * Uses SVG format so Imagick/GD is not required on the server.
     */
    public static function ensure(Permit $permit): void
    {
        if ($permit->qr_code && Storage::disk('public')->exists($permit->qr_code)) {
            return;
        }

        $relativePath = 'permit-qrcodes/'.$permit->getKey().'.svg';

        $svg = QrCode::format('svg')
            ->size(220)
            ->margin(1)
            ->generate(self::verificationUrl($permit));

        Storage::disk('public')->put($relativePath, (string) $svg);

        $permit->forceFill(['qr_code' => $relativePath])->save();
    }

    /**
     * Public URL when storage:link exists (optional fallback).
     */
    public static function publicUrl(Permit $permit): ?string
    {
        self::ensure($permit);
        $permit->refresh();

        if (! $permit->qr_code || ! Storage::disk('public')->exists($permit->qr_code)) {
            return null;
        }

        return Storage::disk('public')->url($permit->qr_code);
    }

    /**
     * Prefer PNG for DomPDF; falls back to stored SVG data URI.
     */
    public static function qrDataUriForDomPdf(Permit $permit): ?string
    {
        self::ensure($permit);

        try {
            $binary = QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->generate(self::verificationUrl($permit));

            return 'data:image/png;base64,'.base64_encode((string) $binary);
        } catch (\Throwable) {
            $permit->refresh();

            return self::imageDataUriFromStorage($permit->qr_code);
        }
    }

    /**
     * Data URI for embedding in Blade (web UI).
     */
    public static function dataUri(Permit $permit): ?string
    {
        self::ensure($permit);
        $permit->refresh();

        return self::imageDataUriFromStorage($permit->qr_code);
    }

    public static function base64StorageFile(?string $relativePath): ?string
    {
        if ($relativePath === null || $relativePath === '' || ! Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        $raw = Storage::disk('public')->get($relativePath);

        return base64_encode($raw);
    }

    public static function imageDataUriFromStorage(?string $relativePath): ?string
    {
        $encoded = static::base64StorageFile($relativePath);
        if ($encoded === null) {
            return null;
        }

        $ext = strtolower(pathinfo((string) $relativePath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/jpeg',
        };

        return 'data:'.$mime.';base64,'.$encoded;
    }
}
