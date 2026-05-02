<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Setting;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::query()->with('account')->latest('date');
        $query->when($request->integer('month'), fn ($q, $month) => $q->whereYear('date', $request->integer('year', now()->year))->whereMonth('date', $month));
        $query->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')));
        $query->when($request->filled('account_id'), fn ($q) => $q->where('account_id', $request->integer('account_id')));
        $query->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')));

        return view('expenses.index', [
            'expenses' => $query->paginate(15)->withQueryString(),
            'monthlyTotal' => (clone $query)->sum('amount_in_myr'),
            'breakdown' => (clone $query)->reorder()->selectRaw('category, SUM(amount_in_myr) as total')->groupBy('category')->orderByDesc('total')->get(),
            'categories' => collect(Setting::get('expense_categories', []))->sort()->values(),
            'accounts' => Account::query()->orderBy('name')->get(),
        ]);
    }


    public function create(): View
    {
        return view('expenses.form', ['expense' => new Expense, 'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(), 'categories' => collect(Setting::get('expense_categories', []))->sort()->values()]);
    }


    public function store(StoreExpenseRequest $request, CurrencyService $currency): RedirectResponse
    {
        $data = $request->validated();
        $data['amount_in_myr'] = $currency->convertToMYR($data['amount'], $data['currency']);
        Expense::query()->create($data);

        return redirect()->route('expenses.index')->with('success', __('expenses.created'));
    }


    public function edit(Expense $expense): View
    {
        return view('expenses.form', ['expense' => $expense, 'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(), 'categories' => collect(Setting::get('expense_categories', []))->sort()->values()]);
    }


    public function update(UpdateExpenseRequest $request, Expense $expense, CurrencyService $currency): RedirectResponse
    {
        $data = $request->validated();
        $data['amount_in_myr'] = $currency->convertToMYR($data['amount'], $data['currency']);
        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', __('expenses.updated'));
    }


    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', __('expenses.deleted'));
    }
}
