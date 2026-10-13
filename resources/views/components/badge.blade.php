@props([
    'variant' => 'slate',
    'size' => 'md',
])

@php
    $variantClasses = match ($variant) {
        'emerald', 'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-emerald-500/10',
        'rose', 'danger', 'error' => 'bg-rose-50 text-rose-700 border-rose-200 ring-rose-500/10',
        'amber', 'warning' => 'bg-amber-50 text-amber-700 border-amber-200 ring-amber-500/10',
        'sky', 'info' => 'bg-sky-50 text-sky-700 border-sky-200 ring-sky-500/10',
        'indigo', 'primary' => 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-indigo-500/10',
        'purple' => 'bg-purple-50 text-purple-700 border-purple-200 ring-purple-500/10',
        default => 'bg-slate-100 text-slate-700 border-slate-200 ring-slate-500/10',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-2 py-0.5 text-xs font-medium',
        'lg' => 'px-3 py-1.5 text-sm font-semibold',
        default => 'px-2.5 py-1 text-xs font-medium',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-md border ring-1 ring-inset {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
