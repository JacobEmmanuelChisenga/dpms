<?php

namespace App\Http\Controllers;

use App\Models\CertificateSetting;
use App\Models\User;
use App\Services\PermitQrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $section = $request->string('section', 'company')->toString();

        $titles = [
            'company' => __('Company Information'),
            'signature' => __('Signature Upload'),
            'preferences' => __('System Preferences'),
            'permit-design' => __('Permit Design'),
        ];

        $certificateSetting = CertificateSetting::singleton();

        return view('settings.index', [
            'section' => $section,
            'sectionTitle' => $titles[$section] ?? __('Settings'),
            'signaturePreviewDataUri' => PermitQrService::imageDataUriFromStorage(
                $certificateSetting->official_signature_path
            ),
        ]);
    }

    /**
     * Store the official scanned signature shown on permit PDF certificates (administration only).
     */
    public function updateSignature(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'signature' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $setting = CertificateSetting::singleton();

        if ($setting->official_signature_path !== null && Storage::disk('public')->exists($setting->official_signature_path)) {
            Storage::disk('public')->delete($setting->official_signature_path);
        }

        $relativePath = $validated['signature']->store('certificate-signatures', 'public');
        $setting->official_signature_path = $relativePath;
        $setting->save();

        CertificateSetting::flushSingletonCache();

        return redirect()
            ->route('settings.index', ['section' => 'signature'])
            ->with('status', __('Certificate signature saved. PDF certificates will include this image.'));
    }

    /**
     * Remove the stored official signature image and clear the path on the certificate settings row (administration only).
     */
    public function destroySignature(): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $setting = CertificateSetting::singleton();

        if ($setting->official_signature_path !== null && Storage::disk('public')->exists($setting->official_signature_path)) {
            Storage::disk('public')->delete($setting->official_signature_path);
        }

        $setting->official_signature_path = null;
        $setting->save();

        CertificateSetting::flushSingletonCache();

        return redirect()
            ->route('settings.index', ['section' => 'signature'])
            ->with('status', __('Certificate signature removed. PDF certificates will show only printed names.'));
    }
}
