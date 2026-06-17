<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <h2 class="text-lg font-semibold text-gray-800">{{ __('User accounts') }}</h2>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="dpms-btn-primary text-xs uppercase tracking-wide">{{ __('Add User') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="dpms-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full dpms-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $row)
                        <tr>
                            <td class="font-medium">{{ $row->name }}</td>
                            <td>{{ $row->email }}</td>
                            <td><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-dpms-50 text-dpms-800">{{ $row->role }}</span></td>
                            <td class="text-end"><a href="{{ route('users.edit', $row) }}" class="text-dpms-700 hover:underline text-sm">{{ __('Edit') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
    </div>

    <div id="roles" class="dpms-card p-6 mt-6">
        <h3 class="font-semibold text-gray-800 mb-3">{{ __('Roles & Permissions') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2">{{ __('Role') }}</th>
                        <th class="py-2">{{ __('Purpose') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="py-2 font-medium">{{ __('Administrator') }}</td><td class="py-2 text-gray-600">{{ __('Full system access — users, settings, audit, oversight') }}</td></tr>
                    <tr><td class="py-2 font-medium">{{ __('Fleet Officer') }}</td><td class="py-2 text-gray-600">{{ __('Daily permit issuance and driver management') }}</td></tr>
                    <tr><td class="py-2 font-medium">{{ __('Management') }}</td><td class="py-2 text-gray-600">{{ __('Reports and verification (read-only oversight)') }}</td></tr>
                    <tr><td class="py-2 font-medium">{{ __('Driver') }}</td><td class="py-2 text-gray-600">{{ __('Portal access / verification only') }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
