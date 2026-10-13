@php
    $messages = [
        'success' => session('success') ?? session('status'),
        'error' => session('error') ?? ($errors->any() ? 'Please correct the errors in the form below.' : null),
        'warning' => session('warning'),
        'info' => session('info'),
    ];
@endphp

@foreach ($messages as $type => $message)
    @if ($message)
        @php
            $config = match ($type) {
                'success' => [
                    'bg' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                    'icon_color' => 'text-emerald-500',
                    'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z',
                ],
                'error' => [
                    'bg' => 'bg-rose-50 border-rose-200 text-rose-800',
                    'icon_color' => 'text-rose-500',
                    'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z',
                ],
                'warning' => [
                    'bg' => 'bg-amber-50 border-amber-200 text-amber-800',
                    'icon_color' => 'text-amber-500',
                    'icon' => 'M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z',
                ],
                default => [
                    'bg' => 'bg-sky-50 border-sky-200 text-sky-800',
                    'icon_color' => 'text-sky-500',
                    'icon' => 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.247.25v3.5a.25.25 0 01-.247.25H9a.75.75 0 000 1.5h2a.75.75 0 000-1.5h-.253a.25.25 0 01-.247-.25v-4.25A.75.75 0 009.75 9H9z',
                ],
            };
        @endphp

        <div class="mb-5 rounded-lg border p-4 shadow-2xs {{ $config['bg'] }}" role="alert">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 shrink-0 {{ $config['icon_color'] }} mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="{{ $config['icon'] }}" clip-rule="evenodd" />
                </svg>
                <div class="flex-1 text-sm">
                    {{ $message }}
                </div>
            </div>
        </div>
    @endif
@endforeach
