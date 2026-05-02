<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::query()
            ->with('client')
            ->withSum('costs as costs_total_myr', 'amount_in_myr')
            ->withSum('income as income_total_myr', 'amount_in_myr')
            ->latest();

        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $query->when($request->filled('billing_type'), fn ($q) => $q->where('billing_type', $request->string('billing_type')));
        $query->when($request->filled('client_id'), fn ($q) => $q->where('client_id', $request->integer('client_id')));
        $query->when($request->filled('search'), function ($q) use ($request): void {
            $search = $request->string('search')->toString();
            $q->where(fn ($inner) => $inner->where('name', 'like', '%'.$search.'%')->orWhereHas('client', fn ($client) => $client->where('name', 'like', '%'.$search.'%')));
        });

        return view('projects.index', [
            'projects' => $query->paginate(15)->withQueryString(),
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }


    public function create(Request $request): View
    {
        return view('projects.form', $this->formData(new Project([
            'client_id' => $request->integer('client_id') ?: null,
            'status' => 'planned',
            'billing_type' => 'paid',
            'currency' => 'MYR',
        ])));
    }


    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::query()->create($request->validated());
        $this->syncLinkedDocuments($project);

        return redirect()->route('projects.show', $project)->with('success', __('projects.created'));
    }


    public function show(Project $project): View
    {
        $project->load(['client', 'quotation', 'invoice', 'costs.account', 'income.account', 'quotations', 'invoices']);

        return view('projects.show', [
            'project' => $project,
            'incomeTotal' => $project->income->sum('amount_in_myr'),
            'costTotal' => $project->costs->sum('amount_in_myr'),
            'billableCostTotal' => $project->costs->where('is_billable', true)->sum('amount_in_myr'),
        ]);
    }


    public function edit(Project $project): View
    {
        return view('projects.form', $this->formData($project));
    }


    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());
        $this->syncLinkedDocuments($project);

        return redirect()->route('projects.show', $project)->with('success', __('projects.updated'));
    }


    public function destroy(Project $project): RedirectResponse
    {
        if ($project->costs()->exists() || $project->income()->exists() || $project->quotations()->exists() || $project->invoices()->exists()) {
            return back()->with('error', __('projects.delete_failed'));
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', __('projects.deleted'));
    }

    /** @return array<string, mixed> */
    private function formData(Project $project): array
    {
        return [
            'project' => $project,
            'clients' => Client::query()->orderBy('name')->get(),
            'currencies' => app(CurrencyService::class)->getAvailableCurrencies(),
            'quotations' => Quotation::query()->with('client')->orderBy('project_title')->orderBy('quotation_number')->get(),
            'invoices' => Invoice::query()->with('client')->orderBy('project_title')->orderBy('invoice_number')->get(),
        ];
    }


    private function syncLinkedDocuments(Project $project): void
    {
        if ($project->quotation_id) {
            Quotation::query()->whereKey($project->quotation_id)->update(['project_id' => $project->id]);
        }

        if ($project->invoice_id) {
            Invoice::query()->whereKey($project->invoice_id)->update(['project_id' => $project->id]);
        }
    }
}
