<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Permits') }}</h2>
            @can('create', App\Models\Permit::class)
                <a href="{{ route('permits.issue') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-xs font-semibold text-white uppercase rounded-md">{{ __('Issue permit') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="dpms-card p-6 space-y-4">
                <form method="get" class="flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('Search') }}</label>
                        <input type="search" name="search" value="{{ request('search') }}" class="rounded-md border-gray-300 text-sm shadow-sm" placeholder="{{ __('Permit # or driver') }}">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('Status') }}</label>
                        <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All') }}</option>
                            <option value="valid" @selected(request('status') === 'valid')>{{ __('Valid') }}</option>
                            <option value="expired" @selected(request('status') === 'expired')>{{ __('Expired') }}</option>
                            <option value="revoked" @selected(request('status') === 'revoked')>{{ __('Revoked') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-200 rounded-md text-sm">{{ __('Filter') }}</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="border-b text-gray-600">
                            <tr>
                                <th class="py-2 pe-4">{{ __('Permit #') }}</th>
                                <th class="py-2 pe-4">{{ __('Driver') }}</th>
                                <th class="py-2 pe-4">{{ __('Valid until') }}</th>
                                <th class="py-2 pe-4">{{ __('Status') }}</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permits as $permit)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pe-4 font-mono">{{ $permit->permit_number }}</td>
                                    <td class="py-2 pe-4">{{ $permit->driver?->full_name }}</td>
                                    <td class="py-2 pe-4">{{ $permit->expiry_date->format('Y-m-d') }}</td>
                                    <td class="py-2 pe-4 uppercase">{{ $permit->status }}</td>
                                    <td class="py-2 text-end"><a href="{{ route('permits.show', $permit) }}" class="text-indigo-600 hover:underline">{{ __('View') }}</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-gray-500">{{ __('No permits found.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $permits->links() }}
            </div>
    </div>
</x-app-layout>
