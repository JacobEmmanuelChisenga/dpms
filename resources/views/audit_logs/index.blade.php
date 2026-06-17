<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Audit log') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <p class="text-sm text-gray-600">{{ __('Append-only record of notable changes to permits, drivers, and account roles.') }}</p>
                <form method="get" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="subject_filter" class="block text-sm text-gray-600 mb-1">{{ __('Subject') }}</label>
                        <select id="subject_filter" name="subject_filter" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All') }}</option>
                            <option value="users" @selected(request('subject_filter') === 'users')>{{ __('Users') }}</option>
                            <option value="drivers" @selected(request('subject_filter') === 'drivers')>{{ __('Drivers') }}</option>
                            <option value="permits" @selected(request('subject_filter') === 'permits')>{{ __('Permits') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="event" class="block text-sm text-gray-600 mb-1">{{ __('Event') }}</label>
                        <select id="event" name="event" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All') }}</option>
                            <option value="created" @selected(request('event') === 'created')>{{ __('created') }}</option>
                            <option value="updated" @selected(request('event') === 'updated')>{{ __('updated') }}</option>
                            <option value="deleted" @selected(request('event') === 'deleted')>{{ __('deleted') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-100 rounded-md text-sm">{{ __('Apply') }}</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left align-top">
                        <thead class="border-b text-gray-600">
                            <tr>
                                <th class="py-2 pe-3">{{ __('When') }}</th>
                                <th class="py-2 pe-3">{{ __('Actor') }}</th>
                                <th class="py-2 pe-3">{{ __('Event') }}</th>
                                <th class="py-2 pe-3">{{ __('Subject') }}</th>
                                <th class="py-2">{{ __('Payload') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pe-3 whitespace-nowrap text-gray-700">{{ $log->logged_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                                    <td class="py-2 pe-3">{{ $log->actor?->email ?? __('System / pre-login') }}</td>
                                    <td class="py-2 pe-3 font-medium">{{ $log->event }}</td>
                                    <td class="py-2 pe-3">
                                        {{ \Illuminate\Support\Str::afterLast((string) $log->subject_type, '\\') }} #{{ $log->subject_id }}
                                    </td>
                                    <td class="py-2 font-mono text-xs text-gray-600 break-all">{{ \Illuminate\Support\Str::limit(json_encode($log->properties ?? []), 520) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-gray-500">{{ __('No audit entries recorded yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
