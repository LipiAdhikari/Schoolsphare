@props([
    'title',
    'value',
    'icon' => null,
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'subtext' => null,
    'isPlaceholder' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden transition-all hover:border-slate-300 hover:shadow-sm']) }}>
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <span class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ $title }}</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</span>
                @if ($change)
                    @php
                        $changeClasses = match ($changeType) {
                            'positive' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                            'negative' => 'text-rose-700 bg-rose-50 border-rose-200',
                            default => 'text-slate-600 bg-slate-100 border-slate-200',
                        };
                    @endphp
                    <span class="inline-flex items-center text-xs font-semibold px-1.5 py-0.5 rounded border {{ $changeClasses }}">
                        {{ $change }}
                    </span>
                @endif
            </div>
            @if ($subtext)
                <p class="text-xs text-slate-500 mt-1">{{ $subtext }}</p>
            @endif
        </div>

        @if ($icon)
            <div class="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                {{ $icon }}
            </div>
        @endif
    </div>

    @if ($isPlaceholder)
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-amber-700 bg-amber-50/50 -mx-5 -mb-5 px-5 py-1.5 border-amber-100">
            <span class="inline-flex items-center gap-1 font-medium">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                Sample metric
            </span>
            <span class="text-slate-500">Awaiting Module Data</span>
        </div>
    @endif
</div>
