<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Account;
use App\Models\Client;
use App\Models\Income;
use App\Models\Project;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Income::query()->with(['client', 'account', 'project'])->latest('date');
        $month = $request->integer('month');
        $year = $request->integer('year', now()->year);
        $query->when($month, fn ($q) => $q->whereYear('date', $year)->whereMonth('date', $month));
        $query->when($request->filled('source'), fn ($q) => $q->where('source', $request->string('source')));
        $query->when($request->filled('account_id'), fn ($q) => $q->where('account_id', $request->integer('account_id')));

        return view('income.index', [
            'income' => $query->paginate(15)->withQueryString(),
            'monthlyTotal' => (clone $query)->sum('amount_in_myr'),
            'accounts' => Account::query()->orderBy('name')->get(),
        ]);
    }


    public function create(): View
    {
        return view('income.form', ['income' => new Income, 'clients' => Client::query()->orderBy('name')->get(), 'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(), 'projects' => Project::query()->with('client')->orderBy('name')->get()]);
    }


    public function store(StoreIncomeRequest $request, CurrencyService $currency): RedirectResponse
    {
        $data = $request->validated();
        $data['amount_in_myr'] = $currency->convertToMYR($data['amount'], $data['currency']);
        Income::query()->create($data);

        return redirect()->route('income.index')->with('success', __('income.created'));
    }


    public function edit(Income $income): View
    {
        return view('income.form', ['income' => $income, 'clients' => Client::query()->orderBy('name')->get(), 'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(), 'projects' => Project::query()->with('client')->orderBy('name')->get()]);
    }


    public function update(UpdateIncomeRequest $request, Income $income, CurrencyService $currency): RedirectResponse
    {
        $data = $request->validated();
        $data['amount_in_myr'] = $currency->convertToMYR($data['amount'], $data['currency']);
        $income->update($data);

        return redirect()->route('income.index')->with('success', __('income.updated'));
    }


    public function destroy(Income $income): RedirectResponse
    {
        $income->delete();

        return redirect()->route('income.index')->with('success', __('income.deleted'));
    }
}
