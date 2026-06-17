<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-800">{{ $sectionTitle }}</h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="dpms-card p-4 lg:col-span-1">
            <nav class="space-y-1 text-sm">
                <a href="{{ route('settings.index', ['section' => 'company']) }}" class="block px-3 py-2 rounded-lg {{ $section === 'company' ? 'bg-dpms-50 text-dpms-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('Company Information') }}</a>
                <a href="{{ route('settings.index', ['section' => 'signature']) }}" class="block px-3 py-2 rounded-lg {{ $section === 'signature' ? 'bg-dpms-50 text-dpms-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('Signature Upload') }}</a>
                <a href="{{ route('settings.index', ['section' => 'preferences']) }}" class="block px-3 py-2 rounded-lg {{ $section === 'preferences' ? 'bg-dpms-50 text-dpms-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('System Preferences') }}</a>
                <a href="{{ route('settings.index', ['section' => 'permit-design']) }}" class="block px-3 py-2 rounded-lg {{ $section === 'permit-design' ? 'bg-dpms-50 text-dpms-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">{{ __('Permit Design') }}</a>
            </nav>
        </div>
        <div class="dpms-card p-6 lg:col-span-3">
            <p class="text-sm text-gray-600 mb-4">{{ __('System configuration for administrators. Changes here affect certificates and how staff distribute printed or emailed permits.') }}</p>
            @if ($section === 'company')
                <div class="space-y-4 max-w-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Organization name') }}</label>
                        <input type="text" value="ZAFFICO PLC" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Department') }}</label>
                        <input type="text" value="Transport / Fleet Office" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" disabled>
                    </div>
                    <p class="text-xs text-amber-700 bg-amber-50 p-3 rounded-lg">{{ __('Persisted settings storage will be added in the next phase (database settings table).') }}</p>
                </div>
            @elseif ($section === 'signature')
                <form method="POST" action="{{ route('settings.signature.update') }}" enctype="multipart/form-data" class="space-y-4 max-w-2xl">
                    @csrf
                    <div class="text-sm text-gray-600 space-y-2">
                        <p>{{ __('Upload one official scanned signature image. It is drawn on every permit PDF above the signing line (the issuer’s printed name is still shown for traceability).') }}</p>
                        <p>{{ __('PNG or JPEG recommended, transparent PNG works best up to about 170×40 px; max size 2 MB. Only administrators can change this file.') }}</p>
                    </div>

                    @if (! empty($signaturePreviewDataUri))
                        <div class="space-y-2">
                            <div class="rounded-lg border border-gray-200 bg-white p-3 inline-block">
                                <p class="text-xs font-medium text-gray-500 mb-2">{{ __('Current signature on certificates') }}</p>
                                <img src="{{ $signaturePreviewDataUri }}" alt="" class="max-h-20 max-w-xs object-contain">
                            </div>
                            <form method="POST" action="{{ route('settings.signature.destroy') }}" class="inline-block" onsubmit="return confirm(@json(__('Remove the signature image from all future permit PDFs?')));">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">{{ __('Remove signature') }}</x-danger-button>
                            </form>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">{{ __('No signature image uploaded yet — PDF will show only the issuer name below the line.') }}</p>
                    @endif

                    <div class="space-y-1">
                        <label for="signature" class="block text-sm font-medium text-gray-700">{{ __('Choose image') }}</label>
                        <input id="signature" type="file" name="signature" accept=".jpg,.jpeg,.png,.webp" required class="block w-full text-sm text-gray-700 file:rounded-md file:border file:border-gray-300 file:bg-white file:px-3 file:py-2 file:text-sm hover:file:bg-gray-50">
                        <x-input-error :messages="$errors->get('signature')" class="mt-2" />
                    </div>

                    <div>
                        <x-primary-button type="submit">{{ __('Save signature') }}</x-primary-button>
                    </div>
                </form>
            @elseif ($section === 'preferences')
                <div class="space-y-3 max-w-lg text-sm">
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled> {{ __('Email notifications for expiring permits') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" checked disabled> {{ __('Require email verification for new users') }}</label>
                    <label class="block pt-2">{{ __('Default permit validity (months)') }} <input type="number" value="12" class="mt-1 rounded-lg border-gray-300 w-24" disabled></label>
                </div>
            @else
                <p class="text-sm text-gray-600">{{ __('Configure permit certificate layout, colors, and footer text. Linked to PDF template in') }} <code class="text-xs bg-gray-100 px-1 rounded">resources/views/permits/pdf-certificate.blade.php</code>.</p>
            @endif
        </div>
    </div>
</x-app-layout>
