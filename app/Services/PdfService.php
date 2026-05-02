<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfService
{
    public function generateQuotationPdf(Quotation $quotation): Response
    {
        $quotation->loadMissing(['client', 'items']);

        return Pdf::loadView('quotations.pdf', ['quotation' => $quotation])->download($quotation->quotation_number.'.pdf');
    }

    public function generateInvoicePdf(Invoice $invoice): Response
    {
        $invoice->loadMissing(['client', 'items', 'paymentAccount']);

        return Pdf::loadView('invoices.pdf', ['invoice' => $invoice])->download($invoice->invoice_number.'.pdf');
    }
}
