<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::query()->withSum('invoices as total_billed', 'total')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search')->toString().'%')->orWhere('company', 'like', '%'.$request->string('search')->toString().'%'))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.form', ['client' => new Client]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::query()->create($request->validated());

        return redirect()->route('clients.index')->with('success', __('clients.created'));
    }

    public function show(Client $client): View
    {
        $client->load(['projects', 'quotations', 'invoices']);

        return view('clients.show', [
            'client' => $client,
            'totalBilled' => $client->invoices->sum('total'),
            'totalPaid' => $client->invoices->where('status', 'paid')->sum('total'),
            'outstanding' => $client->invoices->whereIn('status', ['sent', 'overdue'])->sum('total'),
        ]);
    }

    public function edit(Client $client): View
    {
        return view('clients.form', compact('client'));
    }

    public function update(StoreClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()->route('clients.index')->with('success', __('clients.updated'));
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->income()->exists() || $client->projects()->exists() || $client->quotations()->exists() || $client->invoices()->exists()) {
            return back()->with('error', __('clients.delete_failed'));
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', __('clients.deleted'));
    }
}
