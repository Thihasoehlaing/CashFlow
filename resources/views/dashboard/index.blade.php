<x-layouts::app :title="__('dashboard.title')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a class="btn-secondary" href="{{ $previousMonthUrl }}" aria-label="Previous month">
                    <x-heroicon-o-chevron-left class="size-5" />
                </a>

                <span class="rounded-lg border border-[var(--color-border)] px-4 py-2 font-serif text-xl">
                    {{ $current->format('F Y') }}
                </span>

                <a class="btn-secondary" href="{{ $nextMonthUrl }}" aria-label="Next month">
                    <x-heroicon-o-chevron-right class="size-5" />
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <x-stat-card :label="__('dashboard.net_balance')" :value="'MYR '.number_format($netBalance, 2)" />
            <x-stat-card :label="__('dashboard.net_worth')" :value="'MYR '.number_format($netWorth, 2)" />
            <x-stat-card :label="__('dashboard.expenses')" :value="'MYR '.number_format($thisMonthExpenses, 2)" tone="red" />
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <section class="card">
                <h2 class="mb-4 font-serif text-2xl">{{ __('dashboard.income_vs_expenses') }}</h2>
                <canvas id="monthlyChart" height="150"></canvas>
            </section>

            <section class="card">
                <h2 class="mb-4 font-serif text-2xl">{{ __('dashboard.income_by_source') }}</h2>
                <canvas id="sourceChart" height="150"></canvas>
            </section>
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <section class="card">
                <h2 class="mb-4 font-serif text-2xl">{{ __('dashboard.top_categories') }}</h2>

                <div class="space-y-3">
                    @forelse ($topCategories as $category)
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <span>{{ $category->category }}</span>
                                <span class="font-mono">MYR {{ number_format($category->total, 2) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-[var(--color-border)]">
                                <div class="h-2 rounded-full bg-[var(--color-danger)]" style="width: {{ $topCategories->max('total') ? ($category->total / $topCategories->max('total') * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-[var(--color-text-muted)]">{{ __('common.no_results') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <h2 class="mb-4 font-serif text-2xl">{{ __('dashboard.recent_activity') }}</h2>

                <div class="space-y-3">
                    @foreach ($recent as $row)
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-[var(--color-surface-raised)] p-3">
                            <div>
                                <p>{{ $row['label'] }}</p>
                                <p class="text-sm text-[var(--color-text-muted)]">{{ $row['date']->format('d M Y') }} - {{ $row['description'] }}</p>
                            </div>

                            <span class="font-mono {{ $row['kind'] === 'income' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]' }}">{{ $row['kind'] === 'income' ? '+' : '-' }}{{ $row['currency'] }} {{ number_format($row['amount'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const monthly = @json($monthlySeries);

            new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: monthly.map(row => row.label),
                    datasets: [
                        { label: '{{ __('dashboard.income') }}', data: monthly.map(row => row.income), backgroundColor: '#c9a84c' },
                        { label: '{{ __('dashboard.expenses') }}', data: monthly.map(row => row.expenses), backgroundColor: '#e05252' },
                    ],
                },
                options: {
                    plugins: { legend: { labels: { color: '#f5f5f5' } } },
                    scales: { x: { ticks: { color: '#888' } }, y: { ticks: { color: '#888' } } },
                },
            });

            const sources = @json($incomeBySource);

            new Chart(document.getElementById('sourceChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(sources),
                    datasets: [{ data: Object.values(sources), backgroundColor: ['#c9a84c', '#4caf7d', '#5290e0'] }],
                },
                options: { plugins: { legend: { labels: { color: '#f5f5f5' } } } },
            });
        });
    </script>
</x-layouts::app>
