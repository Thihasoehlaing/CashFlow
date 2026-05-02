<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectCostRequest;
use App\Http\Requests\UpdateProjectCostRequest;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectCostController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('projects.index');
    }

    public function create(Request $request): View
    {
        return view('project-costs.form', $this->formData(new ProjectCost([
            'project_id' => $request->integer('project_id') ?: null,
            'billing_cycle' => 'one_time',
            'currency' => 'MYR',
        ])));
    }

    public function store(StoreProjectCostRequest $request, CurrencyService $currency): RedirectResponse
    {
        $data = $request->validated();
        $data['is_billable'] = $request->boolean('is_billable');
        $data['auto_log_expense'] = $request->boolean('auto_log_expense');
        $data['amount_in_myr'] = $currency->convertToMYR($data['amount'], $data['currency']);

        if ($data['auto_log_expense']) {
            $data['expense_id'] = $this->createExpense($data);
        }

        $projectCost = ProjectCost::query()->create($data);

        return redirect()->route('projects.show', $projectCost->project)->with('success', __('projects.cost_created'));
    }

    public function show(ProjectCost $projectCost): RedirectResponse
    {
        return redirect()->route('projects.show', $projectCost->project);
    }

    public function edit(ProjectCost $projectCost): View
    {
        return view('project-costs.form', $this->formData($projectCost));
    }

    public function update(UpdateProjectCostRequest $request, ProjectCost $projectCost, CurrencyService $currency): RedirectResponse
    {
        $data = $request->validated();
        $data['is_billable'] = $request->boolean('is_billable');
        $data['auto_log_expense'] = $request->boolean('auto_log_expense');
        $data['amount_in_myr'] = $currency->convertToMYR($data['amount'], $data['currency']);

        if ($data['auto_log_expense'] && ! $projectCost->expense_id) {
            $data['expense_id'] = $this->createExpense($data);
        }

        $projectCost->update($data);

        return redirect()->route('projects.show', $projectCost->project)->with('success', __('projects.cost_updated'));
    }

    public function destroy(ProjectCost $projectCost): RedirectResponse
    {
        $project = $projectCost->project;
        $projectCost->delete();

        return redirect()->route('projects.show', $project)->with('success', __('projects.cost_deleted'));
    }

    /** @return array<string, mixed> */
    private function formData(ProjectCost $projectCost): array
    {
        return [
            'projectCost' => $projectCost,
            'projects' => Project::query()->with('client')->orderBy('name')->get(),
            'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(),
            'currencies' => app(CurrencyService::class)->getAvailableCurrencies(),
        ];
    }

    /** @param array<string, mixed> $data */
    private function createExpense(array $data): int
    {
        $expense = Expense::query()->create([
            'category' => 'Infrastructure',
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'amount_in_myr' => $data['amount_in_myr'],
            'account_id' => $data['account_id'],
            'description' => $data['name'],
            'date' => today(),
            'type' => 'business',
        ]);

        return $expense->id;
    }
}
