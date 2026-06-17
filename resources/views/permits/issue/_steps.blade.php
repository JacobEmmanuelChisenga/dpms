@php
    $steps = [
        1 => ['label' => __('Select driver'), 'route' => 'permits.issue'],
        2 => ['label' => __('Validity'), 'route' => 'permits.issue.validity'],
        3 => ['label' => __('Approve'), 'route' => 'permits.issue.review'],
        4 => ['label' => __('Generate'), 'route' => 'permits.issue.generate'],
        5 => ['label' => __('Print'), 'route' => null],
    ];
@endphp

<nav aria-label="{{ __('Issuance progress') }}" class="mb-8">
    <ol class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-0">
        @foreach ($steps as $num => $step)
            @php
                $isComplete = $currentStep > $num;
                $isCurrent = $currentStep === $num;
                $canLink = $isComplete && $step['route'];
            @endphp
            <li class="flex items-center flex-1 min-w-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span @class([
                        'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                        'bg-dpms-700 text-white' => $isCurrent,
                        'bg-green-600 text-white' => $isComplete,
                        'bg-gray-200 text-gray-500' => ! $isCurrent && ! $isComplete,
                    ])>
                        @if ($isComplete)
                            <x-icon name="check" size="sm" />
                        @else
                            {{ $num }}
                        @endif
                    </span>
                    @if ($canLink)
                        <a href="{{ route($step['route']) }}" class="text-sm font-medium text-dpms-700 hover:underline truncate">{{ $step['label'] }}</a>
                    @else
                        <span @class([
                            'text-sm truncate',
                            'font-semibold text-gray-900' => $isCurrent,
                            'text-gray-500' => ! $isCurrent,
                        ])>{{ $step['label'] }}</span>
                    @endif
                </div>
                @if (! $loop->last)
                    <span class="hidden sm:block flex-1 h-px bg-gray-200 mx-3"></span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
