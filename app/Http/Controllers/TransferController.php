<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TransferController extends Controller
{
    public function index(): View
    {
        return view('transfers.index', ['transfers' => Transfer::query()->with(['fromAccount', 'toAccount'])->latest('date')->paginate(15)]);
    }


    public function create(): View
    {
        return view('transfers.form', ['accounts' => Account::query()->where('is_active', true)->orderBy('name')->get()]);
    }


    public function store(StoreTransferRequest $request): RedirectResponse
    {
        Transfer::query()->create($request->validated() + ['fee' => $request->input('fee', 0)]);

        return redirect()->route('transfers.index')->with('success', __('transfers.created'));
    }


    public function destroy(Transfer $transfer): RedirectResponse
    {
        $transfer->delete();

        return redirect()->route('transfers.index')->with('success', __('transfers.deleted'));
    }
}
