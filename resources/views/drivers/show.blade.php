<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $driver->full_name }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @can('update', $driver)
                    <a href="{{ route('drivers.edit', $driver) }}" class="inline-flex items-center px-3 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">{{ __('Edit') }}</a>
                @endcan
                @can('create', App\Models\Permit::class)
                    @if (! $driver->hasActivePermit())
                        <a href="{{ route('permits.create', ['driver_id' => $driver->id]) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md">{{ __('Issue permit') }}</a>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                    <dl class="text-sm space-y-2">
                        <div><dt class="text-gray-500">{{ __('Employee number') }}</dt><dd class="font-mono">{{ $driver->employee_id }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('NRC') }}</dt><dd>{{ $driver->nrc }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Department') }}</dt><dd>{{ $driver->department }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('License') }}</dt><dd>{{ $driver->license_number }} ({{ $driver->license_class }})</dd></div>
                        <div><dt class="text-gray-500">{{ __('Phone') }}</dt><dd>{{ $driver->phone }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Status') }}</dt><dd>@if ($driver->isArchived()) {{ __('Archived') }} @else {{ __('Active') }} @endif</dd></div>
                    </dl>
                    <div class="pt-4 border-t flex flex-col gap-2">
                        @can('archive', $driver)
                            @if (! $driver->isArchived())
                                <form method="post" action="{{ route('drivers.archive', $driver) }}" onsubmit="return confirm(@json(__('Archive this driver?')));">
                                    @csrf
                                    <x-secondary-button type="submit" class="w-full justify-center">{{ __('Archive driver') }}</x-secondary-button>
                                </form>
                            @else
                                <form method="post" action="{{ route('drivers.restore', $driver) }}">
                                    @csrf
                                    <x-primary-button type="submit" class="w-full justify-center">{{ __('Restore driver') }}</x-primary-button>
                                </form>
                            @endif
                        @else
                            @if ($driver->isArchived())
                                <p class="text-sm text-gray-500">{{ __('This driver record is archived.') }}</p>
                            @endif
                        @endcan
                        @can('delete', $driver)
                            <form method="post" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm(@json(__('Delete this driver permanently?')));">
                                @csrf
                                @method('delete')
                                <x-danger-button type="submit" class="w-full justify-center">{{ __('Delete record') }}</x-danger-button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('Permit history') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="border-b text-gray-600">
                                <tr>
                                    <th class="py-2 text-start pe-4">{{ __('Permit #') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Issued') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Expires') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Status') }}</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($driver->permits as $permit)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pe-4 font-mono">{{ $permit->permit_number }}</td>
                                        <td class="py-2 pe-4">{{ $permit->issue_date->format('Y-m-d') }}</td>
                                        <td class="py-2 pe-4">{{ $permit->expiry_date->format('Y-m-d') }}</td>
                                        <td class="py-2 pe-4 uppercase">{{ $permit->status }}</td>
                                        <td class="py-2 text-end"><a href="{{ route('permits.show', $permit) }}" class="text-indigo-600">{{ __('Open') }}</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-gray-500">{{ __('No permits on file.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('drivers.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Back to drivers') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
