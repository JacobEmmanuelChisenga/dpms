<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Reports') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-wrap gap-2">
                <a href="{{ route('reports.index', ['type' => 'active_permits']) }}" class="px-3 py-2 rounded-md text-sm {{ $reportType === 'active_permits' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">{{ __('Active permits') }}</a>
                <a href="{{ route('reports.index', ['type' => 'expired_permits']) }}" class="px-3 py-2 rounded-md text-sm {{ $reportType === 'expired_permits' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">{{ __('Expired permits') }}</a>
                <a href="{{ route('reports.index', ['type' => 'drivers']) }}" class="px-3 py-2 rounded-md text-sm {{ $reportType === 'drivers' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">{{ __('Driver list') }}</a>
                <a href="{{ route('reports.index', ['type' => 'issuance']) }}" class="px-3 py-2 rounded-md text-sm {{ $reportType === 'issuance' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">{{ __('Issuance log') }}</a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ $reportTitle }}</h3>
                <div class="overflow-x-auto">
                    @if ($reportType === 'drivers')
                        <table class="min-w-full text-sm">
                            <thead class="border-b text-gray-600">
                                <tr>
                                    <th class="py-2 text-start pe-4">{{ __('Employee ID') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Name') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Department') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Phone') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pe-4 font-mono">{{ $row->employee_id }}</td>
                                        <td class="py-2 pe-4">{{ $row->full_name }}</td>
                                        <td class="py-2 pe-4">{{ $row->department }}</td>
                                        <td class="py-2 pe-4">{{ $row->phone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif ($reportType === 'issuance')
                        <table class="min-w-full text-sm">
                            <thead class="border-b text-gray-600">
                                <tr>
                                    <th class="py-2 text-start pe-4">{{ __('Permit #') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Driver') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Issued') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Expires') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Issued by') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pe-4 font-mono">{{ $row->permit_number }}</td>
                                        <td class="py-2 pe-4">{{ $row->driver?->full_name }}</td>
                                        <td class="py-2 pe-4">{{ $row->issue_date->format('Y-m-d') }}</td>
                                        <td class="py-2 pe-4">{{ $row->expiry_date->format('Y-m-d') }}</td>
                                        <td class="py-2 pe-4">{{ $row->issuer?->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="min-w-full text-sm">
                            <thead class="border-b text-gray-600">
                                <tr>
                                    <th class="py-2 text-start pe-4">{{ __('Permit #') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Driver') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Expiry') }}</th>
                                    <th class="py-2 text-start pe-4">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pe-4 font-mono">{{ $row->permit_number }}</td>
                                        <td class="py-2 pe-4">{{ $row->driver?->full_name }}</td>
                                        <td class="py-2 pe-4">{{ $row->expiry_date->format('Y-m-d') }}</td>
                                        <td class="py-2 pe-4 uppercase">{{ $row->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="mt-4">{{ $rows->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
