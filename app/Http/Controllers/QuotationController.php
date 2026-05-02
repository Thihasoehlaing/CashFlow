<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Setting;
use App\Services\PdfService;
use App\Services\QuotationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quotation::query()->with(['client', 'project'])->latest('issue_date');
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $query->when($request->filled('client_id'), fn ($q) => $q->where('client_id', $request->integer('client_id')));
        $query->when($request->integer('month'), fn ($q, $month) => $q->whereYear('issue_date', $request->integer('year', now()->year))->whereMonth('issue_date', $month));

        return view('quotations.index', ['quotations' => $query->paginate(15)->withQueryString(), 'clients' => Client::query()->orderBy('name')->get()]);
    }


    public function create(): View
    {
        return view('quotations.form', ['quotation' => new Quotation(['issue_date' => today(), 'valid_until' => today()->addDays((int) Setting::get('default_validity_days', 30))]), 'clients' => Client::query()->orderBy('name')->get(), 'projects' => Project::query()->orderBy('name')->get()]);
    }


    public function store(StoreQuotationRequest $request, QuotationService $service): RedirectResponse
    {
        $data = $request->validated();
        $items = $this->normalizedItems($data['items']);
        $totals = $service->calculateTotals($items, $data['discount_type'] ?? null, $data['discount_value'] ?? 0, $data['tax_rate'] ?? 0);
        $quotation = Quotation::query()->create($data + $totals + [
            'quotation_number' => $service->generateNumber(),
            'business_snapshot' => $this->businessSnapshot(),
        ]);
        $this->syncItems($quotation, $items);

        return redirect()->route('quotations.show', $quotation)->with('success', __('quotations.created'));
    }


    public function show(Quotation $quotation): View
    {
        return view('quotations.show', ['quotation' => $quotation->load(['client', 'project', 'items', 'invoice'])]);
    }


    public function edit(Quotation $quotation): View|RedirectResponse
    {
        if ($quotation->status !== 'draft') {
            return back()->with('error', __('quotations.edit_locked'));
        }

        return view('quotations.form', ['quotation' => $quotation->load('items'), 'clients' => Client::query()->orderBy('name')->get(), 'projects' => Project::query()->orderBy('name')->get()]);
    }


    public function update(UpdateQuotationRequest $request, Quotation $quotation, QuotationService $service): RedirectResponse
    {
        if ($quotation->status !== 'draft') {
            return back()->with('error', __('quotations.edit_locked'));
        }

        $data = $request->validated();
        $items = $this->normalizedItems($data['items']);
        $totals = $service->calculateTotals($items, $data['discount_type'] ?? null, $data['discount_value'] ?? 0, $data['tax_rate'] ?? 0);
        $quotation->update($data + $totals + ['business_snapshot' => $this->businessSnapshot()]);
        $quotation->items()->delete();
        $this->syncItems($quotation, $items);

        return redirect()->route('quotations.show', $quotation)->with('success', __('quotations.updated'));
    }


    public function destroy(Quotation $quotation): RedirectResponse
    {
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', __('quotations.deleted'));
    }


    public function updateStatus(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', 'in:draft,sent,accepted,rejected']]);
        $quotation->update($validated);

        return back()->with('success', __('quotations.status_updated'));
    }


    public function convertToInvoice(Quotation $quotation, QuotationService $service): RedirectResponse
    {
        if ($quotation->status !== 'accepted') {
            return back()->with('error', __('quotations.convert_failed'));
        }

        $invoice = $service->convertToInvoice($quotation);

        return redirect()->route('invoices.edit', $invoice)->with('success', __('invoices.created'));
    }


    public function downloadPdf(Quotation $quotation, PdfService $pdf): Response
    {
        return $pdf->generateQuotationPdf($quotation);
    }

    /** @param array<int, array<string, mixed>> $items @return array<int, array<string, mixed>> */
    private function normalizedItems(array $items): array
    {
        return collect($items)->values()->map(fn (array $item, int $index): array => [
            'description' => $item['description'],
            'item_type' => $item['item_type'],
            'quantity' => (float) $item['quantity'],
            'unit_price' => (float) $item['unit_price'],
            'amount' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
            'sort_order' => $index,
        ])->all();
    }

    /** @param array<int, array<string, mixed>> $items */
    private function syncItems(Quotation $quotation, array $items): void
    {
        foreach ($items as $item) {
            $quotation->items()->create($item);
        }
    }

    /** @return array<string, mixed> */
    private function businessSnapshot(): array
    {
        return [
            'business_name' => Setting::get('business_name', 'CashFlow'),
            'business_email' => Setting::get('business_email', ''),
            'business_phone' => Setting::get('business_phone', ''),
            'business_address' => Setting::get('business_address', ''),
            'business_reg_no' => Setting::get('business_reg_no', ''),
            'business_logo' => Setting::get('business_logo', 'images/logo.png'),
        ];
    }
}
