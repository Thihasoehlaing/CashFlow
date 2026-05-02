<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, CurrencyService $currency): View
    {
        $month = (int) $request->integer('month', (int) now()->month);
        $year = (int) $request->integer('year', (int) now()->year);
        $current = Carbon::create($year, $month, 1);

        $incomeTotal = (float) Income::query()->whereYear('date', $year)->whereMonth('date', $month)->sum('amount_in_myr');
        $expenseTotal = (float) Expense::query()->whereYear('date', $year)->whereMonth('date', $month)->sum('amount_in_myr');

        $months = collect(range(5, 0))->map(function (int $offset): array {
            $date = now()->startOfMonth()->subMonths($offset);

            return [
                'label' => $date->format('M Y'),
                'income' => (float) Income::query()->whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount_in_myr'),
                'expenses' => (float) Expense::query()->whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('amount_in_myr'),
            ];
        });

        $recent = Income::query()->with('account', 'client')->latest('date')->limit(5)->get()->map(fn (Income $income): array => [
            'kind' => 'income', 'date' => $income->date, 'label' => ucfirst($income->source), 'description' => $income->description ?: $income->project_name, 'amount' => $income->amount, 'currency' => $income->currency,
        ])->merge(Expense::query()->with('account')->latest('date')->limit(5)->get()->map(fn (Expense $expense): array => [
            'kind' => 'expense', 'date' => $expense->date, 'label' => $expense->category, 'description' => $expense->description, 'amount' => $expense->amount, 'currency' => $expense->currency,
        ]))->sortByDesc('date')->take(5)->values();

        return view('dashboard.index', [
            'current' => $current,
            'previousMonthUrl' => route('dashboard', ['month' => $current->copy()->subMonth()->month, 'year' => $current->copy()->subMonth()->year]),
            'nextMonthUrl' => route('dashboard', ['month' => $current->copy()->addMonth()->month, 'year' => $current->copy()->addMonth()->year]),
            'netBalance' => $incomeTotal - $expenseTotal,
            'thisMonthExpenses' => $expenseTotal,
            'netWorth' => Account::query()->get()->sum(fn (Account $account): float => $account->current_balance_in_myr),
            'incomeBySource' => Income::query()->selectRaw('source, SUM(amount_in_myr) as total')->whereYear('date', $year)->whereMonth('date', $month)->groupBy('source')->pluck('total', 'source'),
            'monthlySeries' => $months,
            'topCategories' => Expense::query()->selectRaw('category, SUM(amount_in_myr) as total')->whereYear('date', $year)->whereMonth('date', $month)->groupBy('category')->orderByDesc('total')->limit(5)->get(),
            'recent' => $recent,
            'currency' => $currency,
        ]);
    }
}
