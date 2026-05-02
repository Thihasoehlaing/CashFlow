@props(['amount', 'currency' => 'MYR'])
<span class="font-mono text-[var(--color-text)]">{{ $currency }} {{ number_format((float) $amount, 2) }}</span>
