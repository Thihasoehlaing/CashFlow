<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Income;
use App\Models\Project;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class IncomeForm extends Component
{
    public string $source = 'job';
    public ?int $clientId = null;
    public string $billingType = 'fixed';
    public float $hours = 0;
    public float $ratePerHour = 0;
    public float $amount = 0;
    public string $currency = 'MYR';
    public string $projectName = '';
    public ?int $projectId = null;


    public function mount(?Income $income = null): void
    {
        $this->source = old('source', $income?->source ?? 'job');
        $this->clientId = old('client_id', $income?->client_id);
        $this->billingType = old('billing_type', $income?->billing_type ?? 'fixed');
        $this->hours = (float) old('hours', $income?->hours ?? 0);
        $this->ratePerHour = (float) old('rate_per_hour', $income?->rate_per_hour ?? 0);
        $this->amount = (float) old('amount', $income?->amount ?? 0);
        $this->currency = old('currency', $income?->currency ?? 'MYR');
        $this->projectName = old('project_name', $income?->project_name ?? '');
        $this->projectId = old('project_id', $income?->project_id);
        $this->recalculateHourlyAmount();
    }


    public function updatedSource(): void
    {
        if ($this->source !== 'freelance') {
            $this->clientId = null;
            $this->projectId = null;
            $this->billingType = 'fixed';
        }
    }


    public function updatedClientId(): void
    {
        $this->projectId = null;
    }


    public function updatedBillingType(): void
    {
        $this->recalculateHourlyAmount();
    }


    public function updatedHours(): void
    {
        $this->recalculateHourlyAmount();
    }


    public function updatedRatePerHour(): void
    {
        $this->recalculateHourlyAmount();
    }


    public function myrPreview(): string
    {
        $service = app(CurrencyService::class);
        $properties = $this->all();
        $amount = (float) ($properties['amount'] ?? 0);
        $currency = (string) ($properties['currency'] ?? 'MYR');

        return $service->formatAmount($service->convertToMYR($amount, $currency), 'MYR');
    }


    public function projectNameLabel(): string
    {
        return match ($this->source) {
            'job' => __('income.office_company'),
            'family' => __('income.from_who'),
            default => __('income.project_name'),
        };
    }


    private function recalculateHourlyAmount(): void
    {
        $properties = $this->all();

        if (($properties['source'] ?? 'job') === 'freelance' && ($properties['billingType'] ?? 'fixed') === 'hourly') {
            $this->amount = round((float) ($properties['hours'] ?? 0) * (float) ($properties['ratePerHour'] ?? 0), 2);
        }
    }


    public function render(): View
    {
        return view('livewire.income-form', [
            'clients' => Client::query()->orderBy('name')->get(),
            'currencies' => app(CurrencyService::class)->getAvailableCurrencies(),
            'projects' => Project::query()
                ->when($this->clientId, fn ($query) => $query->where('client_id', $this->clientId))
                ->orderBy('name')
                ->get(),
        ]);
    }
}
