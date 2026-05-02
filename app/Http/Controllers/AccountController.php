<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Models\Account;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $query = Account::query()->latest();

        $query->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')));
        $query->when($request->filled('currency'), fn ($query) => $query->where('currency', $request->string('currency')));

        return view('accounts.index', [
            'accounts' => $query->paginate(15)->withQueryString(),
            'currencies' => Account::query()->select('currency')->distinct()->orderBy('currency')->pluck('currency'),
        ]);
    }


    public function create(): View
    {
        return view('accounts.form', ['account' => new Account]);
    }


    public function store(StoreAccountRequest $request): RedirectResponse
    {
        Account::query()->create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('accounts.index')->with('success', __('accounts.created'));
    }


    public function show(Account $account): View
    {
        $transactions = collect()
            ->merge($account->income()->latest('date')->get()->map(fn ($row) => ['date' => $row->date, 'kind' => 'income', 'label' => $row->source, 'amount' => $row->amount, 'currency' => $row->currency]))
            ->merge($account->expenses()->latest('date')->get()->map(fn ($row) => ['date' => $row->date, 'kind' => 'expense', 'label' => $row->category, 'amount' => -$row->amount, 'currency' => $row->currency]))
            ->merge($account->incomingTransfers()->latest('date')->get()->map(fn ($row) => ['date' => $row->date, 'kind' => 'transfer', 'label' => $row->fromAccount?->name.' ? '.$account->name, 'amount' => $row->to_amount, 'currency' => $row->to_currency]))
            ->merge($account->outgoingTransfers()->latest('date')->get()->map(fn ($row) => ['date' => $row->date, 'kind' => 'transfer', 'label' => $account->name.' ? '.$row->toAccount?->name, 'amount' => -$row->from_amount, 'currency' => $row->from_currency]))
            ->sortByDesc('date')->values();

        return view('accounts.show', compact('account', 'transactions'));
    }


    public function edit(Account $account): View
    {
        return view('accounts.form', compact('account'));
    }


    public function update(StoreAccountRequest $request, Account $account): RedirectResponse
    {
        $account->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('accounts.index')->with('success', __('accounts.updated'));
    }


    public function destroy(Account $account): RedirectResponse
    {
        if ($account->income()->exists() || $account->expenses()->exists() || $account->incomingTransfers()->exists() || $account->outgoingTransfers()->exists()) {
            return back()->with('error', __('accounts.delete_failed'));
        }

        $account->delete();

        return redirect()->route('accounts.index')->with('success', __('accounts.deleted'));
    }
}
