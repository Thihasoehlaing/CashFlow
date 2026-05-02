@props(['action', 'method' => 'DELETE', 'label' => null])
<div x-data="{ open: false, shaking: false }" class="inline-block">
    <button type="button" @click="open = true" class="text-[var(--color-danger)] hover:underline">{{ $label ?? __('common.delete') }}</button>
    <div x-cloak x-show="open" class="fixed inset-0 z-50 grid place-items-end bg-black/70 p-4 sm:place-items-center" @click.self="open = false">
        <form method="POST" action="{{ $action }}" x-bind:class="shaking ? 'shake' : ''" class="w-full max-w-sm rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 shadow-2xl">
            @csrf
            @method($method)
            <h2 class="font-serif text-2xl">{{ __('common.confirm_delete') }}</h2>
            <p class="mt-2 text-sm text-[var(--color-text-muted)]">{{ __('common.delete_warning') }}</p>
            <div class="mt-5 flex justify-end gap-2"><button type="button" class="btn-secondary" @click="open = false">{{ __('common.cancel') }}</button><button class="btn-danger">{{ __('common.delete') }}</button></div>
        </form>
    </div>
</div>
