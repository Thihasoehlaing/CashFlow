<?php

use App\Services\InvoiceService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn (): int => app(InvoiceService::class)->checkOverdue())
    ->name('invoices-check-overdue')
    ->daily();
