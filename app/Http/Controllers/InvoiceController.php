<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Account;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Setting;
use App\Services\InvoiceService;
use App\Services\PdfService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::query()->with(['client', 'project'])->latest('issue_date');
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));
        $query->when($request->filled('client_id'), fn ($q) => $q->where('client_id', $request->integer('client_id')));
        $query->when($request->integer('month'), fn ($q, $month) => $q->whereYear('issue_date', $request->integer('year', now()->year))->whereMonth('issue_date', $month));

        return view('invoices.index', ['invoices' => $query->paginate(15)->withQueryString(), 'clients' => Client::query()->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('invoices.form', ['invoice' => new Invoice(['issue_date' => today(), 'due_date' => today()->addDays(14)]), 'clients' => Client::query()->orderBy('name')->get(), 'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(), 'projects' => Project::query()->orderBy('name')->get()]);
    }

    public function store(StoreInvoiceRequest $request, InvoiceService $service): RedirectResponse
    {
        $data = $request->validated();
        $items = $this->normalizedItems($data['items']);
        $totals = $service->calculateTotals($items, $data['discount_type'] ?? null, $data['discount_value'] ?? 0, $data['tax_rate'] ?? 0);
        $invoice = Invoice::query()->create($data + $totals + [
            'invoice_number' => $service->generateNumber(),
            'business_snapshot' => $this->businessSnapshot(),
        ]);
        $this->syncItems($invoice, $items);

        return redirect()->route('invoices.show', $invoice)->with('success', __('invoices.created'));
    }

    public function show(Invoice $invoice): View
    {
        return view('invoices.show', [
            'invoice' => $invoice->load(['client', 'project', 'items', 'paymentAccount']),
            'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function edit(Invoice $invoice): View|RedirectResponse
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', __('invoices.edit_locked'));
        }

        return view('invoices.form', ['invoice' => $invoice->load('items'), 'clients' => Client::query()->orderBy('name')->get(), 'accounts' => Account::query()->where('is_active', true)->orderBy('name')->get(), 'projects' => Project::query()->orderBy('name')->get()]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice, InvoiceService $service): RedirectResponse
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', __('invoices.edit_locked'));
        }

        $data = $request->validated();
        $items = $this->normalizedItems($data['items']);
        $totals = $service->calculateTotals($items, $data['discount_type'] ?? null, $data['discount_value'] ?? 0, $data['tax_rate'] ?? 0);
        $invoice->update($data + $totals + ['business_snapshot' => $this->businessSnapshot()]);
        $invoice->items()->delete();
        $this->syncItems($invoice, $items);

        return redirect()->route('invoices.show', $invoice)->with('success', __('invoices.updated'));
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', __('invoices.deleted'));
    }

    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', 'in:draft,sent,paid,overdue']]);
        $invoice->update($validated);

        return back()->with('success', __('invoices.status_updated'));
    }

    public function markAsPaid(Request $request, Invoice $invoice, InvoiceService $service): RedirectResponse
    {
        $validated = $request->validate([
            'paid_at' => ['required', 'date'],
            'payment_account_id' => ['required', 'exists:accounts,id'],
            'log_income' => ['nullable', 'boolean'],
        ]);
        $service->markAsPaid($invoice, (int) $validated['payment_account_id'], $validated['paid_at'], $request->boolean('log_income'));

        return back()->with('success', __('invoices.paid'));
    }

    public function downloadPdf(Invoice $invoice, PdfService $pdf): Response
    {
        return $pdf->generateInvoicePdf($invoice);
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
    private function syncItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            $invoice->items()->create($item);
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
