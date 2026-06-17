<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-800">{{ __('Historical Records') }}</h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('archives.permits') }}" class="dpms-card p-6 hover:shadow-md transition block">
            <h3 class="font-semibold text-gray-800">{{ __('Old Permits') }}</h3>
            <p class="text-sm text-gray-500 mt-2">{{ __('Expired and long-term permit records retained for compliance.') }}</p>
        </a>
        <a href="{{ route('archives.drivers') }}" class="dpms-card p-6 hover:shadow-md transition block">
            <h3 class="font-semibold text-gray-800">{{ __('Archived Drivers') }}</h3>
            <p class="text-sm text-gray-500 mt-2">{{ __('Drivers removed from active fleet operations.') }}</p>
        </a>
        <a href="{{ route('audit-logs.index') }}" class="dpms-card p-6 hover:shadow-md transition block">
            <h3 class="font-semibold text-gray-800">{{ __('Audit Trail') }}</h3>
            <p class="text-sm text-gray-500 mt-2">{{ __('Complete system change history for accountability.') }}</p>
        </a>
    </div>
</x-app-layout>
