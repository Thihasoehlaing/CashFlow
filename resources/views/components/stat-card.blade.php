@props(['label', 'value', 'tone' => 'gold'])
<div class="card animate-card">
    <p class="text-sm text-[var(--color-text-muted)]">{{ $label }}</p>
    <p class="mt-3 font-mono text-2xl font-semibold {{ $tone === 'red' ? 'text-[var(--color-danger)]' : 'text-[var(--color-primary)]' }}" x-data="{ shown: false }" x-init="requestAnimationFrame(() => shown = true)" x-text="shown ? '{{ $value }}' : '0.00'"></p>
</div>
