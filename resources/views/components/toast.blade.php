<div class="fixed right-4 top-20 z-50 space-y-3" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)">
    @foreach (['success' => 'border-[var(--color-primary)] text-[var(--color-primary)]', 'error' => 'border-[var(--color-danger)] text-[var(--color-danger)]'] as $key => $class)
        @if (session($key))
            <div x-show="show" x-transition class="toast {{ $class }}">{{ session($key) }}</div>
        @endif
    @endforeach
</div>
